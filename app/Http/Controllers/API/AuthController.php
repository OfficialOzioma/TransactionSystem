<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User login to obtain API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $token = $this->authService->login($request->only('email', 'password'));
            return response()->json(['status' => true, 'message' => 'Login successful', 'token' => $token], 200);
        } catch (ValidationException $e) {
            return response()->json([ 'status' => false, 'message' => $e->errors()], 401);
        }
    }

    /**
     * User registration to obtain API token.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $registration = $this->authService->register($request->only('name', 'email', 'password'));

        if ($registration === false) {
            return response()->json(['status' => false, 'message' => 'Registration failed, please try again.'], 422);
        }
    
        return response()->json([ "status" => true, 'message' => 'Registration successful, please login to continue'], 201);
    }

    /**
     * User logout by revoking API token.
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
