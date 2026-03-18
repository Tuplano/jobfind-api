<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user  = $this->authService->register($request->validated());
        $token = $this->authService->createToken($user);

        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'user'    => UserResource::make($user),
            'token'   => $token,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data  = $request->validated();
        $user  = $this->authService->login($data, $data['role']);
        $token = $this->authService->createToken($user);

        return response()->json([
            'message' => 'Login successful.',
            'user'    => UserResource::make($user),
            'token'   => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($request->user()->load('employeeProfile', 'employerProfile')),
        ]);
    }
}
