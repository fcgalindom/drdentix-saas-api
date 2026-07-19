<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'guard_name' => ['sometimes', 'string', 'max:255'],
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
                'description' => 'The permission key. Use dotted notation for grouping (e.g., resource.action).',
                'example' => 'citas.listar',
            ],
            'guard_name' => [
                'description' => 'The guard name. Defaults to "web".',
                'example' => 'web',
            ],
        ];
    }
}
