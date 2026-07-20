<?php

namespace App\Http\Requests\Procedure;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:procedures,id'],
            'name' => ['required', 'string', 'max:70'],
            'duration' => ['required', 'integer', 'min:1'],
            'state' => ['sometimes', 'string'],
        ];
    }
}
