<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles')->ignore($this->route('role'))],
            'guard_name' => ['sometimes', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The display name of the role.',
                'example' => 'Administrador General',
            ],
            'guard_name' => [
                'description' => 'The guard name.',
                'example' => 'web',
            ],
            'permissions' => [
                'description' => 'Array of permission IDs to replace existing ones.',
            ],
        ];
    }
}
