<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * List all users.
     *
     * Returns all registered users with their assigned roles and permissions.
     *
     * @authenticated
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('roles.permissions')->get();

        return UserResource::collection($users);
    }

    /**
     * Show a specific user.
     *
     * Returns a user's details along with their roles and permissions.
     *
     * @authenticated
     *
     * @urlParam user integer required The user ID. Example: 1
     */
    public function show(User $user): UserResource
    {
        $user->load('roles.permissions');

        return new UserResource($user);
    }

    /**
     * Assign roles to a user.
     *
     * Replaces all existing roles on the user with the given set.
     *
     * @authenticated
     *
     * @urlParam user integer required The user ID. Example: 1
     *
     * @bodyParam roles array required Array of role IDs to assign. Example: [1, 2]
     * @bodyParam roles.* integer required A role ID. Example: 1
     */
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Roles assigned successfully',
            'user' => new UserResource($user->load('roles.permissions')),
        ]);
    }

    /**
     * Get effective permissions for a user.
     *
     * Returns all permissions the user has, aggregated from all their assigned roles.
     *
     * @authenticated
     *
     * @urlParam user integer required The user ID. Example: 1
     */
    public function permissions(User $user): JsonResponse
    {
        $permissions = $user->getAllPermissions();

        return response()->json(['permissions' => $permissions]);
    }
}
