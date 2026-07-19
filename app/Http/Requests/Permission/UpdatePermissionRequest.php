<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('permissions')->ignore($this->route('permission'))],
            'guard_name' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The permission key. Use dotted notation for grouping.',
                'example' => 'citas.ver',
            ],
            'guard_name' => [
                'description' => 'The guard name.',
                'example' => 'web',
            ],
        ];
    }
}
