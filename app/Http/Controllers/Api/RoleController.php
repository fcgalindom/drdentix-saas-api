<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * List all roles.
     *
     * Returns a list of all roles with their associated permissions.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = Role::with('permissions')->get();

        return RoleResource::collection($roles);
    }

    /**
     * Create a new role.
     *
     * Creates a role and optionally assigns permissions to it.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the role. Example: Secretaria
     * @bodyParam guard_name string The guard name. Defaults to "web". Example: web
     * @bodyParam permissions array Array of permission IDs to assign. Example: [1, 6, 10]
     * @bodyParam permissions.* integer A permission ID. Example: 1
     *
     * @responseField message string Confirmation message.
     * @responseField role object The created role with permissions.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->only('name', 'guard_name'));

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'role' => new RoleResource($role->load('permissions')),
        ], 201);
    }

    /**
     * Show a specific role.
     *
     * Returns the details of a single role along with its permissions.
     *
     * @authenticated
     *
     * @urlParam role integer required The role ID. Example: 1
     */
    public function show(Role $role): RoleResource
    {
        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Update a role.
     *
     * Updates the role's name and/or its permissions.
     *
     * @authenticated
     *
     * @urlParam role integer required The role ID. Example: 1
     *
     * @bodyParam name string The new name for the role. Example: Administrador General
     * @bodyParam guard_name string The guard name. Example: web
     * @bodyParam permissions array Array of permission IDs to replace existing ones. Example: [1, 2, 3]
     * @bodyParam permissions.* integer A permission ID. Example: 1
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->only('name', 'guard_name'));

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => new RoleResource($role->load('permissions')),
        ]);
    }

    /**
     * Delete a role.
     *
     * Permanently removes the role from the system.
     *
     * @authenticated
     *
     * @urlParam role integer required The role ID. Example: 3
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }

    /**
     * Sync permissions for a role.
     *
     * Replaces all existing permissions on the role with the given set.
     *
     * @authenticated
     *
     * @urlParam role integer required The role ID. Example: 2
     *
     * @bodyParam permissions array required Array of permission IDs to assign. Example: [1, 6, 10]
     * @bodyParam permissions.* integer required A permission ID. Example: 1
     */
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permissions synchronized successfully',
            'role' => new RoleResource($role->load('permissions')),
        ]);
    }
}
