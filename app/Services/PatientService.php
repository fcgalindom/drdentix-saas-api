<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PatientService extends Service
{
    public function __construct(Patient $patient)
    {
        parent::__construct($patient);
    }

    public function listAll(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with('user')
            ->withCount(['appointments as paid_count' => fn ($q) => $q->where('state', 'Pagado')]);

        if ($name = $filters['name'] ?? null) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($city = $filters['city'] ?? null) {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($document = $filters['document'] ?? null) {
            $query->whereHas('user', fn ($q) => $q->where('document', 'like', "%{$document}%"));
        }

        return $query->orderByDesc('paid_count')->paginate(15);
    }

    public function save(array $data, ?int $id = null): Patient
    {
        return DB::transaction(function () use ($data, $id) {
            if ($id) {
                $patient = $this->model->findOrFail($id);
                $patient->update([
                    'name' => $data['name'],
                    'city' => $data['city'] ?? null,
                    'telephone' => $data['telephone'],
                ]);
                $patient->user->update(array_filter([
                    'document' => $data['document'],
                    'email' => $data['email'] ?? null,
                    'birth' => $data['birth'] ?? null,
                ]));
            } else {
                $user = User::create([
                    'document' => $data['document'],
                    'email' => $data['email'] ?? null,
                    'password' => $this->hashPassword('1234'),
                    'type_user' => 'Patient',
                    'state' => 'Activo',
                    'birth' => $data['birth'] ?? null,
                ]);
                $patient = $this->model->create([
                    'name' => $data['name'],
                    'city' => $data['city'] ?? null,
                    'telephone' => '+57'.ltrim($data['telephone'], '+57'),
                    'id_user' => $user->id,
                ]);
            }

            return $patient->load('user');
        });
    }

    public function findById(int $id): Patient
    {
        return $this->model->with('user')->findOrFail($id);
    }

    public function me(User $user): Patient
    {
        return $this->model->with('user')
            ->where('id_user', $user->id)
            ->firstOrFail();
    }

    public function deactivate(int $id, string $state): void
    {
        $patient = $this->model->with('user')->findOrFail($id);
        $patient->user->update([
            'state' => $state,
            'email' => time().'@gmail.com',
            'document' => (string) time(),
        ]);
    }

    public function select(): Collection
    {
        return $this->model->with('user')
            ->whereHas('user', fn ($q) => $q->where('state', 'Activo'))
            ->get()
            ->map(fn ($p) => ['id' => $p->id, 'text' => $p->user->document.' - '.$p->name]);
    }

    public function findByDocument(string $document): array
    {
        $user = User::where('document', $document)->where('type_user', 'Patient')->first();

        if (! $user) {
            return ['status' => 422];
        }

        return ['status' => 200, 'id' => $user->patient->id];
    }
}
