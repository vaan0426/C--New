<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Register a new frontend user. Account/bookings become valid only
     * after the e-mail activation link is confirmed (spec §4.1, §7.6).
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_USER,
            'status' => User::STATUS_ACTIVE,
        ]);

        event(new Registered($user));

        return response()->json([
            'message' => 'Регистрацията е успешна. Проверете имейла си за активация.',
            'user' => $user,
        ], 201);
    }

    /**
     * Log in. Only frontend roles reach the SPA; the admin app checks role after login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Невалиден имейл или парола.'], 422);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Моля, потвърдете имейла си преди вход.'], 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Излязохте от профила си.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Handle the signed e-mail verification link.
     */
    public function verifyEmail(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Невалиден линк за активация.'], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['message' => 'Имейлът е потвърден успешно.']);
    }
}
