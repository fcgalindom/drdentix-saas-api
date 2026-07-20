<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day' => ['required', 'date', 'after_or_equal:today'],
            'hour' => ['required', 'string'],
            'branch_id' => ['required', 'exists:branches,id'],
            'patient_id' => ['required', 'exists:patients,id'],
            'dentist_procedure_id' => ['required', 'exists:dentist_procedures,id'],
            'type' => ['nullable', 'integer'],
        ];
    }
}
