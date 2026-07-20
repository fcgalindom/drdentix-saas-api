<?php

namespace App\Http\Requests\Dentist;

use Illuminate\Foundation\Http\FormRequest;

class StoreDentistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:dentists,id'],
            'name' => ['required', 'string', 'max:70'],
            'city' => ['required', 'string', 'max:70'],
            'document' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:70'],
            'birth' => ['nullable', 'date', 'before:today'],
            'password' => ['nullable', 'string', 'min:6'],
            'procedure_ids' => ['nullable', 'array'],
            'procedure_ids.*' => ['exists:procedures,id'],
        ];
    }
}
