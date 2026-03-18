<?php

namespace App\Http\Controllers\Api\V1\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\UpdateProfileRequest;
use App\Http\Resources\EmployeeProfileResource;
use App\Services\EmployeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly EmployeeService $employeeService) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $this->employeeService->getOrCreateProfile($request->user());

        return response()->json([
            'profile' => EmployeeProfileResource::make($profile->load('skills')),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->employeeService->updateProfile($request->user(), $request->validated());

        return response()->json([
            'message' => 'Profile updated.',
            'profile' => EmployeeProfileResource::make($profile),
        ]);
    }

    public function uploadResume(Request $request): JsonResponse
    {
        $request->validate([
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $result = $this->employeeService->uploadResume($request->user(), $request->file('resume'));

        return response()->json([
            'message' => 'Resume uploaded.',
            'data'    => $result,
        ]);
    }

    public function completeSetup(Request $request): JsonResponse
    {
        $profile = $this->employeeService->markSetupComplete($request->user());

        return response()->json([
            'message' => 'Setup completed.',
            'profile' => EmployeeProfileResource::make($profile),
        ]);
    }
}
