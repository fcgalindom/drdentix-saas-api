<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return PermissionResource::collection($this->permissionService->listAll());
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionService->createPermission($request->validated());

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => new PermissionResource($permission),
        ], 201);
    }

    public function show(int $id): PermissionResource
    {
        return new PermissionResource($this->permissionService->find($id));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $updated = $this->permissionService->updatePermission($permission, $request->validated());

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => new PermissionResource($updated),
        ]);
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $this->permissionService->deletePermission($permission);

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
