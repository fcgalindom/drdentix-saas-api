<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\DentistProcedureResource;
use App\Models\Appointment;
use App\Models\DentistProcedure;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    // Form bootstrap data (branches, procedures, patients for dropdowns)
    public function formData(Request $request)
    {
        return response()->json([
            'branches'   => \App\Models\Branch::where('state', 'Activo')->orderBy('name')->get(['id', 'name']),
            'procedures' => \App\Models\Procedure::where('state', 'Activo')->orderBy('name')->get(['id', 'name', 'duration']),
            'patients'   => Patient::with('user')
                ->whereHas('user', fn($q) => $q->where('state', 'Activo'))
                ->get()->map(fn($p) => ['id' => $p->id, 'text' => $p->user->document . ' - ' . $p->name]),
            'min_date'   => now()->toDateString(),
        ]);
    }

    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure'])
            ->notDeleted();

        if ($request->filled('patient')) {
            $query->whereHas('patient', fn($q) => $q->where('name', 'like', '%' . $request->patient . '%'))
                ->orWhereHas('patient.user', fn($q) => $q->where('document', 'like', '%' . $request->patient . '%'));
        }
        if ($request->filled('date_from')) {
            $query->where('day', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('day', '<=', $request->date_to);
        }
        if ($request->filled('dentist_id')) {
            $query->whereHas('dentistProcedure', fn($q) => $q->where('dentist_id', $request->dentist_id));
        }
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        // Default to today if no date filters given
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $offset = (int) $request->get('advance', 0);
            $date = now()->addDays($offset)->toDateString();
            $query->where('day', $date);
        }

        $appointments = $query->orderByRaw("FIELD(state, 'Activo', 'Recordado', 'Cancelado', 'No asistio', 'Pagado')")
            ->paginate(15);

        $income = Appointment::notDeleted()->sum('pay');
        $pending = Appointment::whereIn('state', ['Activo', 'Recordado'])->count();

        return response()->json([
            'data'    => AppointmentResource::collection($appointments)->response()->getData(true),
            'income'  => $income,
            'pending' => $pending,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'day'                  => 'required|date|after_or_equal:today',
            'hour'                 => 'required|string',
            'branch_id'            => 'required|exists:branches,id',
            'patient_id'           => 'required|exists:patients,id',
            'dentist_procedure_id' => 'required|exists:dentist_procedures,id',
            'type'                 => 'nullable|integer', // 1 = admin force-book
        ]);

        $isAdmin = $request->user()->type_user === 'Administrator';
        $forceBook = ($data['type'] ?? 0) == 1 && $isAdmin;

        // Block if patient has an active/recorded appointment (unless admin force-books)
        if (!$forceBook) {
            $existing = Appointment::where('patient_id', $data['patient_id'])
                ->whereIn('state', ['Activo', 'Recordado'])
                ->first();

            if ($existing) {
                return response()->json(['message' => 'El paciente ya tiene una cita activa.'], 422);
            }
        }

        $appointment = Appointment::create([
            'day'                  => $data['day'],
            'hour'                 => $data['hour'],
            'branch_id'            => $data['branch_id'],
            'patient_id'           => $data['patient_id'],
            'dentist_procedure_id' => $data['dentist_procedure_id'],
            'state'                => 'Activo',
        ]);

        return new AppointmentResource($appointment->load(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure']));
    }

    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure', 'invoices.procedure']));
    }

    // Change state + create invoices (billing flow)
    public function changeState(Request $request)
    {
        $request->validate([
            'id'        => 'required|exists:appointments,id',
            'state'     => 'required|string',
            'payments'  => 'nullable|array',
            'payments.*.price'        => 'required|numeric',
            'payments.*.procedure_id' => 'required|exists:procedures,id',
        ]);

        $appointment = Appointment::with(['patient.user', 'invoices'])->findOrFail($request->id);
        $state = $request->state === 'Asistio' ? 'Pagado' : $request->state;

        $totalPay = 0;
        if ($request->filled('payments') && $state === 'Pagado') {
            foreach ($request->payments as $payment) {
                Invoice::create([
                    'price'          => $payment['price'],
                    'procedure_id'   => $payment['procedure_id'],
                    'appointment_id' => $appointment->id,
                ]);
                $totalPay += $payment['price'];
            }
            $appointment->pay = $totalPay;
        }

        $appointment->state = $state;
        $appointment->save();

        return new AppointmentResource($appointment->load(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure', 'invoices.procedure']));
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|exists:appointments,id']);

        $appointment = Appointment::findOrFail($request->id);

        if ($appointment->state === 'Pagado') {
            return response()->json(['message' => 'No se puede eliminar una cita pagada.'], 422);
        }

        $appointment->update(['state' => 'Eliminado']);

        return response()->json(['message' => 'Cita eliminada.']);
    }

    public function cancel(Request $request)
    {
        $request->validate(['id' => 'required|exists:appointments,id']);

        $appointment = Appointment::where('id', $request->id)
            ->where('patient_id', $request->user()->patient->id)
            ->firstOrFail();

        $appointment->update(['state' => 'Cancelado']);

        return response()->json(['message' => 'Cita cancelada.']);
    }

    public function byPatient(Request $request)
    {
        $patientId = $request->filled('patient_id')
            ? $request->patient_id
            : $request->user()->patient->id;

        $appointments = Appointment::with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->where('patient_id', $patientId)
            ->orderByDesc('day')
            ->paginate(15);

        return AppointmentResource::collection($appointments);
    }

    public function byDocument(Request $request)
    {
        $request->validate(['document' => 'required|string']);

        $appointments = Appointment::with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->whereHas('patient.user', fn($q) => $q->where('document', $request->document))
            ->orderByDesc('day')
            ->paginate(15);

        return AppointmentResource::collection($appointments);
    }

    // Available time slots for a dentist + procedure + date
    public function availableSlots(Request $request)
    {
        $request->validate([
            'dentist_procedure_id' => 'required|exists:dentist_procedures,id',
            'date'                 => 'required|date|after_or_equal:today',
        ]);

        $dentistProcedure = DentistProcedure::with(['dentist.schedules', 'procedure'])->findOrFail($request->dentist_procedure_id);
        $date = \Carbon\Carbon::parse($request->date);

        if ($date->isSunday()) {
            return response()->json(['message' => 'No hay atención los domingos.'], 422);
        }

        $dayOfWeek = $date->dayOfWeekIso; // 1=Mon ... 6=Sat
        $schedule = $dentistProcedure->dentist->schedules->where('day', $dayOfWeek)->where('attend', true)->first();

        if (!$schedule) {
            return response()->json(['slots' => []]);
        }

        $duration = $dentistProcedure->procedure->duration;
        $existing = Appointment::where('dentist_procedure_id', $request->dentist_procedure_id)
            ->where('day', $date->toDateString())
            ->whereIn('state', ['Activo', 'Recordado'])
            ->pluck('hour')
            ->toArray();

        $slots = $this->generateSlots($schedule, $duration, $existing);

        return response()->json(['slots' => $slots]);
    }

    private function generateSlots(object $schedule, int $duration, array $booked): array
    {
        $slots = [];
        $current = \Carbon\Carbon::createFromTimeString($schedule->hour_start);
        $end = \Carbon\Carbon::createFromTimeString($schedule->hour_end);
        $breakStart = $schedule->break && $schedule->break_start ? \Carbon\Carbon::createFromTimeString($schedule->break_start) : null;
        $breakEnd = $schedule->break && $schedule->break_end ? \Carbon\Carbon::createFromTimeString($schedule->break_end) : null;

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $current->copy()->addMinutes($duration);

            // Skip if overlaps break
            if ($breakStart && $current->lt($breakEnd) && $slotEnd->gt($breakStart)) {
                $current = $breakEnd->copy();
                continue;
            }

            $hourStr = $current->format('g:i a');
            if (!in_array($hourStr, $booked)) {
                $slots[] = ['hour_start' => $hourStr, 'hour_end' => $slotEnd->format('g:i a')];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }

    // Dentists that can perform a procedure + their schedules
    public function dentistsByProcedure(Request $request)
    {
        $request->validate(['procedure_id' => 'required|exists:procedures,id']);

        $dentistProcedures = DentistProcedure::with(['dentist.user', 'dentist.schedules'])
            ->where('procedure_id', $request->procedure_id)
            ->get();

        return DentistProcedureResource::collection($dentistProcedures);
    }

    // Mark WhatsApp reminder as sent (frontend sends the actual wa.me link)
    public function markWhatsapp(Request $request)
    {
        $request->validate(['id' => 'required|exists:appointments,id']);

        $appointment = Appointment::with(['patient.user', 'branch', 'dentistProcedure.dentist', 'dentistProcedure.procedure'])->findOrFail($request->id);
        $appointment->update(['type_state' => 1]);

        $dp = $appointment->dentistProcedure;
        $message = "Recordatorio DrDentix:\n"
            . "Fecha: {$appointment->day->format('d/m/Y')}\n"
            . "Hora: {$appointment->hour}\n"
            . "Procedimiento: {$dp->procedure->name}\n"
            . "Odontólogo: {$dp->dentist->name}\n"
            . "Sede: {$appointment->branch->name} — {$appointment->branch->address}";

        return response()->json(['message' => $message]);
    }

    public function markPhone(Request $request)
    {
        $request->validate(['id' => 'required|exists:appointments,id']);

        Appointment::findOrFail($request->id)->update(['type_state' => 2]);

        return response()->json(['message' => 'Llamada registrada.']);
    }
}
