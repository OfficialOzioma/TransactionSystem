<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserCreated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Handle user login and return a token.
     */
    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Generate a new token
        return $user->createToken('API Token')->plainTextToken;
    }

    /**
     * Handle user registration and return a token.
     */
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // if registration failed, return false
        if (!$user) {
            return false;
        }
        return true;
    }

    /**
     * Logout the user by revoking their current token.
     */
    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }
}
