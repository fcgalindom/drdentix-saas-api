<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:patients,id'],
            'name' => ['required', 'string', 'max:70'],
            'city' => ['nullable', 'string', 'max:70'],
            'telephone' => ['required', 'string', 'max:70'],
            'document' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:70'],
            'birth' => ['nullable', 'date', 'before:today'],
        ];
    }
}
