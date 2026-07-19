<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DentistResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Dentist;
use App\Models\DentistProcedure;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DentistController extends Controller
{
    public function index(Request $request)
    {
        $query = Dentist::with(['user', 'procedures'])
            ->whereHas('user', fn($q) => $q->where('state', 'Activo'));

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        if ($request->filled('document')) {
            $query->whereHas('user', fn($q) => $q->where('document', 'like', '%' . $request->document . '%'));
        }

        return DentistResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'           => 'nullable|exists:dentists,id',
            'name'         => 'required|string|max:70',
            'city'         => 'required|string|max:70',
            'document'     => 'required|string|max:20',
            'email'        => 'nullable|email|max:70',
            'birth'        => 'nullable|date|before:today',
            'password'     => 'nullable|string|min:6',
            'procedure_ids' => 'nullable|array',
            'procedure_ids.*' => 'exists:procedures,id',
        ]);

        return DB::transaction(function () use ($request, $data) {
            if ($request->filled('id')) {
                $dentist = Dentist::findOrFail($request->id);
                $dentist->update(['name' => $data['name'], 'city' => $data['city']]);
                $dentist->user->update(array_filter([
                    'document' => $data['document'],
                    'email'    => $data['email'] ?? null,
                    'birth'    => $data['birth'] ?? null,
                ]));
            } else {
                $user = User::create([
                    'document'  => $data['document'],
                    'email'     => $data['email'] ?? null,
                    'password'  => Hash::make($data['password'] ?? '1234'),
                    'type_user' => 'Dentist',
                    'state'     => 'Activo',
                    'birth'     => $data['birth'] ?? null,
                ]);
                $dentist = Dentist::create([
                    'name'    => $data['name'],
                    'city'    => $data['city'],
                    'id_user' => $user->id,
                ]);
            }

            if ($request->filled('procedure_ids')) {
                DentistProcedure::where('dentist_id', $dentist->id)->delete();
                foreach ($data['procedure_ids'] as $procedureId) {
                    DentistProcedure::create([
                        'dentist_id'   => $dentist->id,
                        'procedure_id' => $procedureId,
                    ]);
                }
            }

            return new DentistResource($dentist->load(['user', 'procedures']));
        });
    }

    public function show(Dentist $dentist)
    {
        return new DentistResource($dentist->load(['user', 'procedures', 'schedules']));
    }

    public function changeState(Request $request)
    {
        $request->validate(['id' => 'required|exists:dentists,id', 'state' => 'required|string']);

        $dentist = Dentist::with('user')->findOrFail($request->id);
        $dentist->user->update(['state' => $request->state]);

        return new DentistResource($dentist->load('user'));
    }

    // Select list for dropdowns
    public function select()
    {
        return Dentist::with('user')
            ->whereHas('user', fn($q) => $q->where('state', 'Activo'))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // Schedule management
    public function getSchedule(Request $request)
    {
        $dentistId = $request->filled('dentist_id')
            ? $request->dentist_id
            : $request->user()->dentist->id;

        $schedules = Schedule::where('dentist_id', $dentistId)->get();

        return ScheduleResource::collection($schedules);
    }

    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'dentist_id'    => 'required|exists:dentists,id',
            'schedules'     => 'required|array|min:1',
            'schedules.*.day'         => 'required|integer|between:1,6',
            'schedules.*.attend'      => 'required|boolean',
            'schedules.*.hour_start'  => 'nullable|date_format:H:i',
            'schedules.*.hour_end'    => 'nullable|date_format:H:i',
            'schedules.*.break'       => 'nullable|boolean',
            'schedules.*.break_start' => 'nullable|date_format:H:i',
            'schedules.*.break_end'   => 'nullable|date_format:H:i',
        ]);

        foreach ($data['schedules'] as $slot) {
            Schedule::updateOrCreate(
                ['dentist_id' => $data['dentist_id'], 'day' => $slot['day']],
                $slot + ['dentist_id' => $data['dentist_id']]
            );
        }

        $schedules = Schedule::where('dentist_id', $data['dentist_id'])->get();

        return ScheduleResource::collection($schedules);
    }

    // Dentist's own appointments (fixes the hardcoded bug from original)
    public function myAppointments(Request $request)
    {
        $dentist = $request->user()->dentist;

        if (!$dentist) {
            return response()->json(['message' => 'Perfil de odontólogo no encontrado.'], 404);
        }

        $query = $dentist->dentistProcedures()
            ->with(['appointments.patient.user', 'appointments.branch', 'procedure'])
            ->get()
            ->pluck('appointments')
            ->flatten();

        if ($request->filled('date')) {
            $query = $query->where('day', $request->date);
        }

        return response()->json($query->values());
    }
}
