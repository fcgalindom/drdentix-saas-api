<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\SyncPermissionsRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection($this->roleService->listAll());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return response()->json([
            'message' => 'Role created successfully',
            'role' => new RoleResource($role),
        ], 201);
    }

    public function show(int $id): RoleResource
    {
        return new RoleResource($this->roleService->findById($id));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $updated = $this->roleService->updateRole($role, $request->validated());

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => new RoleResource($updated),
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->deleteRole($role);

        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function syncPermissions(SyncPermissionsRequest $request, Role $role): JsonResponse
    {
        $updated = $this->roleService->syncPermissions($role, $request->permissions);

        return response()->json([
            'message' => 'Permissions synchronized successfully',
            'role' => new RoleResource($updated),
        ]);
    }
}
