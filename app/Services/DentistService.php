<?php

namespace App\Services;

use App\Models\Dentist;
use App\Models\DentistProcedure;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DentistService extends Service
{
    public function __construct(Dentist $dentist)
    {
        parent::__construct($dentist);
    }

    public function listAll(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with(['user', 'procedures'])
            ->whereHas('user', fn ($q) => $q->where('state', 'Activo'));

        if ($name = $filters['name'] ?? null) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($city = $filters['city'] ?? null) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($document = $filters['document'] ?? null) {
            $query->whereHas('user', fn ($q) => $q->where('document', 'like', "%{$document}%"));
        }

        return $query->paginate(15);
    }

    public function save(array $data, ?int $id = null): Dentist
    {
        return DB::transaction(function () use ($data, $id) {
            if ($id) {
                $dentist = $this->model->findOrFail($id);
                $dentist->update(['name' => $data['name'], 'city' => $data['city']]);
                $dentist->user->update(array_filter([
                    'document' => $data['document'],
                    'email' => $data['email'] ?? null,
                    'birth' => $data['birth'] ?? null,
                ]));
            } else {
                $user = User::create([
                    'document' => $data['document'],
                    'email' => $data['email'] ?? null,
                    'password' => $this->hashPassword($data['password'] ?? '1234'),
                    'type_user' => 'Dentist',
                    'state' => 'Activo',
                    'birth' => $data['birth'] ?? null,
                ]);
                $dentist = $this->model->create([
                    'name' => $data['name'],
                    'city' => $data['city'],
                    'id_user' => $user->id,
                ]);
            }

            if (isset($data['procedure_ids'])) {
                DentistProcedure::where('dentist_id', $dentist->id)->delete();
                foreach ($data['procedure_ids'] as $procedureId) {
                    DentistProcedure::create([
                        'dentist_id' => $dentist->id,
                        'procedure_id' => $procedureId,
                    ]);
                }
            }

            return $dentist->load(['user', 'procedures']);
        });
    }

    public function findById(int $id): Dentist
    {
        return $this->model->with(['user', 'procedures', 'schedules'])->findOrFail($id);
    }

    public function changeState(int $id, string $state): Dentist
    {
        $dentist = $this->model->with('user')->findOrFail($id);
        $dentist->user->update(['state' => $state]);

        return $dentist->load('user');
    }

    public function select(): Collection
    {
        return $this->model->with('user')
            ->whereHas('user', fn ($q) => $q->where('state', 'Activo'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getSchedule(?int $dentistId, ?User $user): Collection
    {
        $id = $dentistId ?? $user?->dentist?->id;

        return Schedule::where('dentist_id', $id)->get();
    }

    public function saveSchedule(array $data): Collection
    {
        foreach ($data['schedules'] as $slot) {
            Schedule::updateOrCreate(
                ['dentist_id' => $data['dentist_id'], 'day' => $slot['day']],
                $slot + ['dentist_id' => $data['dentist_id']],
            );
        }

        return Schedule::where('dentist_id', $data['dentist_id'])->get();
    }

    public function myAppointments(User $user, ?string $date): Collection
    {
        $dentist = $user->dentist;

        if (! $dentist) {
            abort(404, 'Perfil de odontólogo no encontrado.');
        }

        $query = $dentist->dentistProcedures()
            ->with(['appointments.patient.user', 'appointments.branch', 'procedure'])
            ->get()
            ->pluck('appointments')
            ->flatten();

        if ($date) {
            $query = $query->where('day', $date);
        }

        return $query->values();
    }
}
