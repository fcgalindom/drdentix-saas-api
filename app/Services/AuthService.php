<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\User;

class AuthService extends Service
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function login(array $credentials): array
    {
        $user = $this->model->where('email', $credentials['email'])->first();

        if (! $user || ! $this->checkPassword($credentials['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        return $this->createTokenResponse($user, 'auth-token');
    }

    public function loginPatient(array $credentials): array
    {
        $user = $this->model
            ->where('email', $credentials['email'])
            ->where('type_user', 'Patient')
            ->first();

        if (! $user || ! $this->checkPassword($credentials['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        return $this->createTokenResponse($user, 'patient-token', loadRoles: false);
    }

    public function loginStaff(array $credentials): array
    {
        $user = $this->model
            ->where('email', $credentials['email'])
            ->whereIn('type_user', ['Administrator', 'Dentist'])
            ->first();

        if (! $user || ! $this->checkPassword($credentials['password'], $user->password)) {
            abort(401, 'Invalid credentials');
        }

        return $this->createTokenResponse($user, 'staff-token');
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function me(User $user): User
    {
        return $user->load('roles.permissions');
    }

    public function updatePhoto(User $user, string $photo): void
    {
        $user->update(['photo' => $photo]);
    }

    private function createTokenResponse(User $user, string $tokenName, bool $loadRoles = true): array
    {
        $token = $user->createToken($tokenName)->plainTextToken;

        $user->load($loadRoles ? 'roles.permissions' : []);

        return [
            'token' => $token,
            'user' => new UserResource($user),
        ];
    }
}
