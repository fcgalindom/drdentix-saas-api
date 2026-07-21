<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\DentistProcedure;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AppointmentService extends Service
{
    public function __construct(Appointment $appointment)
    {
        parent::__construct($appointment);
    }

    public function formData(): array
    {
        return [
            'branches' => Branch::where('state', 'Activo')->orderBy('name')->get(['id', 'name']),
            'procedures' => Procedure::where('state', 'Activo')->orderBy('name')->get(['id', 'name', 'duration']),
            'patients' => Patient::with('user')
                ->whereHas('user', fn ($q) => $q->where('state', 'Activo'))
                ->get()->map(fn ($p) => ['id' => $p->id, 'text' => $p->user->document.' - '.$p->name]),
            'min_date' => now()->toDateString(),
        ];
    }

    public function listAll(array $filters): array
    {
        $query = $this->model->with(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure'])
            ->notDeleted();

        if ($patient = $filters['patient'] ?? null) {
            $query->whereHas('patient', fn ($q) => $q->where('name', 'like', "%{$patient}%"))
                ->orWhereHas('patient.user', fn ($q) => $q->where('document', 'like', "%{$patient}%"));
        }
        if ($dateFrom = $filters['date_from'] ?? null) {
            $query->where('day', '>=', $dateFrom);
        }
        if ($dateTo = $filters['date_to'] ?? null) {
            $query->where('day', '<=', $dateTo);
        }
        if ($dentistId = $filters['dentist_id'] ?? null) {
            $query->whereHas('dentistProcedure', fn ($q) => $q->where('dentist_id', $dentistId));
        }
        if ($state = $filters['state'] ?? null) {
            $query->where('state', $state);
        }

        if (! isset($filters['date_from']) && ! isset($filters['date_to'])) {
            $offset = (int) ($filters['advance'] ?? 0);
            $date = now()->addDays($offset)->toDateString();
            $query->where('day', $date);
        }

        $appointments = $query->orderByRaw("CASE state WHEN 'Activo' THEN 1 WHEN 'Recordado' THEN 2 WHEN 'Cancelado' THEN 3 WHEN 'No asistio' THEN 4 WHEN 'Pagado' THEN 5 ELSE 6 END")
            ->paginate(15);

        $income = $this->model->notDeleted()->sum('pay');
        $pending = $this->model->whereIn('state', ['Activo', 'Recordado'])->count();

        return [
            'appointments' => $appointments,
            'income' => $income,
            'pending' => $pending,
        ];
    }

    public function createAppointment(array $data, User $user): Appointment
    {
        $isAdmin = $user->type_user === 'Administrator';
        $forceBook = ($data['type'] ?? 0) == 1 && $isAdmin;

        if (! $forceBook) {
            $existing = $this->model->where('patient_id', $data['patient_id'])
                ->whereIn('state', ['Activo', 'Recordado'])
                ->first();

            if ($existing) {
                abort(422, 'El paciente ya tiene una cita activa.');
            }
        }

        $appointment = $this->model->create([
            'day' => $data['day'],
            'hour' => $data['hour'],
            'branch_id' => $data['branch_id'],
            'patient_id' => $data['patient_id'],
            'dentist_procedure_id' => $data['dentist_procedure_id'],
            'state' => 'Activo',
        ]);

        return $appointment->load(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure']);
    }

    public function findById(int $id): Appointment
    {
        return $this->model
            ->with(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure', 'invoices.procedure'])
            ->findOrFail($id);
    }

    public function changeState(int $id, string $state, ?array $payments): Appointment
    {
        $appointment = $this->model->with(['patient.user', 'invoices'])->findOrFail($id);
        $resolvedState = $state === 'Asistio' ? 'Pagado' : $state;

        $totalPay = 0;
        if ($payments && $resolvedState === 'Pagado') {
            foreach ($payments as $payment) {
                Invoice::create([
                    'price' => $payment['price'],
                    'procedure_id' => $payment['procedure_id'],
                    'appointment_id' => $appointment->id,
                ]);
                $totalPay += $payment['price'];
            }
            $appointment->pay = $totalPay;
        }

        $appointment->state = $resolvedState;
        $appointment->save();

        return $appointment->load(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure', 'invoices.procedure']);
    }

    public function deleteAppointment(int $id): void
    {
        $appointment = $this->find($id);

        if ($appointment->state === 'Pagado') {
            abort(422, 'No se puede eliminar una cita pagada.');
        }

        $appointment->update(['state' => 'Eliminado']);
    }

    public function cancelAppointment(int $id, User $user): void
    {
        $appointment = $this->model
            ->where('id', $id)
            ->where('patient_id', $user->patient->id)
            ->firstOrFail();

        $appointment->update(['state' => 'Cancelado']);
    }

    public function byPatient(?int $patientId, User $user): LengthAwarePaginator
    {
        $id = $patientId ?? $user->patient->id;

        return $this->model
            ->with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->where('patient_id', $id)
            ->orderByDesc('day')
            ->paginate(15);
    }

    public function byDocument(string $document): LengthAwarePaginator
    {
        return $this->model
            ->with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->whereHas('patient.user', fn ($q) => $q->where('document', $document))
            ->orderByDesc('day')
            ->paginate(15);
    }

    public function availableSlots(int $dentistProcedureId, string $date): array
    {
        $dentistProcedure = DentistProcedure::with(['dentist.schedules', 'procedure'])->findOrFail($dentistProcedureId);
        $carbonDate = Carbon::parse($date);

        if ($carbonDate->isSunday()) {
            abort(422, 'No hay atención los domingos.');
        }

        $dayOfWeek = $carbonDate->dayOfWeekIso;
        $schedule = $dentistProcedure->dentist->schedules
            ->where('day', $dayOfWeek)
            ->where('attend', true)
            ->first();

        if (! $schedule) {
            return [];
        }

        $duration = $dentistProcedure->procedure->duration;
        $booked = $this->model
            ->where('dentist_procedure_id', $dentistProcedureId)
            ->where('day', $carbonDate->toDateString())
            ->whereIn('state', ['Activo', 'Recordado'])
            ->pluck('hour')
            ->toArray();

        return $this->generateSlots($schedule, $duration, $booked);
    }

    public function dentistsByProcedure(int $procedureId): Collection
    {
        return DentistProcedure::with(['dentist.user', 'dentist.schedules'])
            ->where('procedure_id', $procedureId)
            ->get();
    }

    public function markWhatsapp(int $id): array
    {
        $appointment = $this->model
            ->with(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure'])
            ->findOrFail($id);

        $appointment->update(['type_state' => 1]);

        $dp = $appointment->dentistProcedure;
        $message = "Recordatorio DrDentix:\n"
            ."Fecha: {$appointment->day->format('d/m/Y')}\n"
            ."Hora: {$appointment->hour}\n"
            ."Procedimiento: {$dp->procedure->name}\n"
            ."Odontólogo: {$dp->dentist->name}\n"
            ."Sede: {$appointment->branch->name} — {$appointment->branch->address}";

        return ['message' => $message];
    }

    public function markPhone(int $id): void
    {
        $this->find($id)->update(['type_state' => 2]);
    }

    private function generateSlots(object $schedule, int $duration, array $booked): array
    {
        $slots = [];
        $current = Carbon::createFromTimeString($schedule->hour_start);
        $end = Carbon::createFromTimeString($schedule->hour_end);
        $breakStart = $schedule->break && $schedule->break_start ? Carbon::createFromTimeString($schedule->break_start) : null;
        $breakEnd = $schedule->break && $schedule->break_end ? Carbon::createFromTimeString($schedule->break_end) : null;

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($duration);

            if ($breakStart && $current->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                $current = $breakEnd->copy();

                continue;
            }

            $hourStr = $current->format('g:i a');
            if (! in_array($hourStr, $booked)) {
                $slots[] = ['hour_start' => $hourStr, 'hour_end' => $slotEnd->format('g:i a')];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }
}
