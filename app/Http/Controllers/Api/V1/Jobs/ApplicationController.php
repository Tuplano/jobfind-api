<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applicationService) {}

    public function apply(StoreApplicationRequest $request, Job $job): JsonResponse
    {
        $application = $this->applicationService->apply($request->user(), $job, $request->validated());

        return response()->json([
            'message'     => 'Application submitted.',
            'application' => ApplicationResource::make($application),
        ], 201);
    }

    public function myApplications(Request $request): JsonResponse
    {
        $applications = $this->applicationService->getForEmployee($request->user());

        return response()->json(
            ApplicationResource::collection($applications)->response()->getData(true)
        );
    }

    public function jobApplications(Request $request, Job $job): JsonResponse
    {
        $this->authorize('view', $job);

        $applications = $this->applicationService->getForJob($job);

        return response()->json(
            ApplicationResource::collection($applications)->response()->getData(true)
        );
    }

    public function updateStatus(Request $request, Application $application): JsonResponse
    {
        $this->authorize('update', $application);

        $request->validate([
            'status' => ['required', 'in:reviewed,shortlisted,rejected,hired'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $application = $this->applicationService->updateStatus(
            $application,
            $request->status,
            $request->notes
        );

        return response()->json([
            'message'     => 'Application status updated.',
            'application' => ApplicationResource::make($application),
        ]);
    }
}
