<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate user and return token.
     *
     * Receives email and password, validates credentials, and returns a Sanctum token
     * along with the authenticated user's data including roles and permissions.
     *
     * @unauthenticated
     *
     * @responseField token string The Sanctum API token. Include this in the Authorization header for subsequent requests.
     * @responseField user object The authenticated user's data with roles and permissions.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user->load('roles.permissions')),
        ]);
    }

    /**
     * Logout and revoke current token.
     *
     * Invalidates the current Bearer token. The token will no longer be accepted.
     *
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get the authenticated user's profile.
     *
     * Returns the current user's information including their assigned roles and permissions.
     *
     * @authenticated
     */
    public function me(Request $request): UserResource
    {
        $user = $request->user()->load('roles.permissions');

        return new UserResource($user);
    }
}
