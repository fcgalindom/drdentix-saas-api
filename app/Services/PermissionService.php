<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionService extends Service
{
    public function __construct(Permission $permission)
    {
        parent::__construct($permission);
    }

    public function listAll(): Collection
    {
        return $this->model->all();
    }

    public function createPermission(array $data): Permission
    {
        return $this->model->create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'] ?? $permission->name,
            'guard_name' => $data['guard_name'] ?? $permission->guard_name,
        ]);

        return $permission->fresh();
    }

    public function deletePermission(Permission $permission): void
    {
        $permission->delete();
    }
}
