<?php

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

class BillingByPatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
        ];
    }
}
