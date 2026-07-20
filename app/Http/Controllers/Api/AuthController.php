<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginPatientRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginStaffRequest;
use App\Http\Requests\Auth\UpdatePhotoRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json($result);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($this->authService->me($request->user()));
    }

    public function loginPatient(LoginPatientRequest $request): JsonResponse
    {
        $result = $this->authService->loginPatient($request->validated());

        return response()->json($result);
    }

    public function loginStaff(LoginStaffRequest $request): JsonResponse
    {
        $result = $this->authService->loginStaff($request->validated());

        return response()->json($result);
    }

    public function updatePhoto(UpdatePhotoRequest $request): JsonResponse
    {
        $this->authService->updatePhoto($request->user(), $request->photo);

        return response()->json(['message' => 'Photo updated successfully']);
    }
}
