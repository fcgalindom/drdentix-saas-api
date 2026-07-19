<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Active staff summary for dashboard graphs
    public function staffGraph()
    {
        $users = User::with('dentist')
            ->where('state', 'Activo')
            ->get();

        return response()->json($users);
    }

    // Paginated paid appointments for a patient (billing history)
    public function billingByPatient(Request $request)
    {
        $request->validate(['patient_id' => 'required|exists:patients,id']);

        $appointments = Appointment::with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->where('patient_id', $request->patient_id)
            ->where('state', 'Pagado')
            ->orderByDesc('day')
            ->paginate(15);

        return AppointmentResource::collection($appointments);
    }
}
