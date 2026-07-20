<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReportService extends Service
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function staffGraph(): Collection
    {
        return $this->model->with('dentist')
            ->where('state', 'Activo')
            ->get();
    }

    public function billingByPatient(int $patientId): LengthAwarePaginator
    {
        return Appointment::with(['dentistProcedure.dentist', 'dentistProcedure.procedure', 'branch', 'invoices.procedure'])
            ->where('patient_id', $patientId)
            ->where('state', 'Pagado')
            ->orderByDesc('day')
            ->paginate(15);
    }
}
