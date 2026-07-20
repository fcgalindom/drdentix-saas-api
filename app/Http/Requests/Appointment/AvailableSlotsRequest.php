<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AvailableSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dentist_procedure_id' => ['required', 'exists:dentist_procedures,id'],
            'date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
