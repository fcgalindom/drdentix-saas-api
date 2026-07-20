<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService extends Service
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function listAll(): Collection
    {
        return $this->model->with('roles.permissions')->get();
    }

    public function findById(int $id): User
    {
        $user = $this->find($id);
        $user->load('roles.permissions');

        return $user;
    }

    public function assignRoles(User $user, array $roles): User
    {
        $user->syncRoles($roles);

        return $user->load('roles.permissions');
    }

    public function permissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }
}
