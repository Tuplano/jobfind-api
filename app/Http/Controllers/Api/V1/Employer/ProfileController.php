<?php

namespace App\Http\Controllers\Api\V1\Employer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employer\UpdateProfileRequest;
use App\Http\Resources\EmployerProfileResource;
use App\Services\EmployerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly EmployerService $employerService) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->employerService->getOrCreateProfile($request->user());

        return response()->json([
            'profile' => EmployerProfileResource::make($profile),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->employerService->updateProfile($request->user(), $request->validated());

        return response()->json([
            'message' => 'Profile updated.',
            'profile' => EmployerProfileResource::make($profile),
        ]);
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $result = $this->employerService->uploadLogo($request->user(), $request->file('logo'));

        return response()->json([
            'message' => 'Logo uploaded.',
            'data'    => $result,
        ]);
    }

    public function completeSetup(Request $request): JsonResponse
    {
        $profile = $this->employerService->markSetupComplete($request->user());

        return response()->json([
            'message' => 'Setup completed.',
            'profile' => EmployerProfileResource::make($profile),
        ]);
    }
}
