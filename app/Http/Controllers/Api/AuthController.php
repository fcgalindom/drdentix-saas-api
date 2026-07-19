<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Patient login by document number only (no password)
    public function loginPatient(Request $request)
    {
        $request->validate(['document' => 'required|string']);

        $user = User::where('document', $request->document)
            ->where('type_user', 'Patient')
            ->first();

        if (!$user || !$user->isActive()) {
            return response()->json(['message' => 'Credenciales inválidas o cuenta inactiva.'], 401);
        }

        $token = $user->createToken('patient-token', ['role:patient'])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    // Admin / Dentist login by email + password
    public function loginStaff(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->whereIn('type_user', ['Administrator', 'Dentist'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        if (!$user->isActive()) {
            return response()->json(['message' => 'Cuenta inactiva.'], 403);
        }

        $token = $user->createToken('staff-token', ['role:' . strtolower($user->type_user)])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function updatePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:3072']);

        $path = $request->file('photo')->store('uploads', 'public');
        $request->user()->update(['photo' => '/storage/' . $path]);

        return response()->json(['photo' => '/storage/' . $path]);
    }
}
