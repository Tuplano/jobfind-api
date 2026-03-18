<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => $data['role'],
        ]);

        event(new Registered($user));

        return $user;
    }

    public function login(array $credentials, string $role): User
    {
        $user = User::where('email', $credentials['email'])
            ->where('role', $role)
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your email address before logging in.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Your account has been deactivated.'],
            ]);
        }

        return $user;
    }

    public function createToken(User $user): string
    {
        return $user->createToken('api-token', [$user->role->value])->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
