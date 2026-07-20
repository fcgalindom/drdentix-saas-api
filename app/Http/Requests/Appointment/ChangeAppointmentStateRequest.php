<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class ChangeAppointmentStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'exists:appointments,id'],
            'state' => ['required', 'string'],
            'payments' => ['nullable', 'array'],
            'payments.*.price' => ['required', 'numeric'],
            'payments.*.procedure_id' => ['required', 'exists:procedures,id'],
        ];
    }
}
