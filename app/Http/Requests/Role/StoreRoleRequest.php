<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'guard_name' => ['sometimes', 'string', 'max:255'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('guard_name')) {
            $this->merge(['guard_name' => 'web']);
        }
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The display name of the role.',
                'example' => 'Secretaria',
            ],
            'guard_name' => [
                'description' => 'The guard name. Defaults to "web".',
                'example' => 'web',
            ],
            'permissions' => [
                'description' => 'Array of permission IDs to assign to the role.',
            ],
        ];
    }
}
