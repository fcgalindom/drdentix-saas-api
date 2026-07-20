<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:70'],
            'address' => ['required', 'string', 'max:70'],
            'contact' => ['required', 'string', 'max:70'],
            'city' => ['required', 'string', 'max:70'],
            'state' => ['sometimes', 'string'],
        ];
    }
}
