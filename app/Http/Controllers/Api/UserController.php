<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AssignRolesRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection($this->userService->listAll());
    }

    public function show(int $id): UserResource
    {
        return new UserResource($this->userService->findById($id));
    }

    public function assignRoles(AssignRolesRequest $request, User $user): JsonResponse
    {
        $updated = $this->userService->assignRoles($user, $request->roles);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => new UserResource($updated),
        ]);
    }

    public function permissions(User $user): JsonResponse
    {
        return response()->json([
            'permissions' => $this->userService->permissions($user),
        ]);
    }
}
