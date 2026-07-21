<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::create(['name' => 'roles.listar', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.crear', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.editar', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.eliminar', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.asignar-permisos', 'guard_name' => 'web']);

        Permission::create(['name' => 'permisos.listar', 'guard_name' => 'web']);
        Permission::create(['name' => 'permisos.crear', 'guard_name' => 'web']);
        Permission::create(['name' => 'permisos.editar', 'guard_name' => 'web']);
        Permission::create(['name' => 'permisos.eliminar', 'guard_name' => 'web']);

        Permission::create(['name' => 'usuarios.listar', 'guard_name' => 'web']);
        Permission::create(['name' => 'usuarios.asignar-roles', 'guard_name' => 'web']);

        $admin = Role::create(['name' => 'Administrador', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all());

        Role::create(['name' => 'Dentist', 'guard_name' => 'web']);
        Role::create(['name' => 'Patient', 'guard_name' => 'web']);
    }
}
