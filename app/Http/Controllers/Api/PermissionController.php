<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * List all permissions.
     *
     * Returns a list of all permissions in the system.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        return PermissionResource::collection(Permission::all());
    }

    /**
     * Create a new permission.
     *
     * Creates a new permission that can be assigned to roles.
     *
     * @authenticated
     *
     * @bodyParam name string required The name of the permission. Use dotted notation for grouping. Example: citas.listar
     * @bodyParam guard_name string The guard name. Defaults to "web". Example: web
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = Permission::create($request->only('name', 'guard_name'));

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => new PermissionResource($permission),
        ], 201);
    }

    /**
     * Show a specific permission.
     *
     * @authenticated
     *
     * @urlParam permission integer required The permission ID. Example: 1
     */
    public function show(Permission $permission): PermissionResource
    {
        return new PermissionResource($permission);
    }

    /**
     * Update a permission.
     *
     * @authenticated
     *
     * @urlParam permission integer required The permission ID. Example: 12
     *
     * @bodyParam name string The new name for the permission. Example: citas.ver
     * @bodyParam guard_name string The guard name. Example: web
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission->update($request->only('name', 'guard_name'));

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => new PermissionResource($permission->fresh()),
        ]);
    }

    /**
     * Delete a permission.
     *
     * Permanently removes the permission. This will revoke it from all roles that have it.
     *
     * @authenticated
     *
     * @urlParam permission integer required The permission ID. Example: 12
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
