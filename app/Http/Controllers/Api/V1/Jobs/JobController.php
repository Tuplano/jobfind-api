<?php

namespace App\Http\Controllers\Api\V1\Jobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Jobs\StoreJobRequest;
use App\Http\Requests\Jobs\UpdateJobRequest;
use App\Http\Resources\JobResource;
use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(private readonly JobService $jobService) {}

    public function index(Request $request): JsonResponse
    {
        $jobs = $this->jobService->listActive(
            $request->only(['search', 'type', 'experience_level', 'location', 'is_remote']),
            (int) $request->get('per_page', 15)
        );

        return response()->json(JobResource::collection($jobs)->response()->getData(true));
    }

    public function show(Job $job): JsonResponse
    {
        $job->load(['employer.employerProfile', 'skills']);

        return response()->json(['job' => JobResource::make($job)]);
    }

    public function store(StoreJobRequest $request): JsonResponse
    {
        $job = $this->jobService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Job listing created.',
            'job'     => JobResource::make($job),
        ], 201);
    }

    public function update(UpdateJobRequest $request, Job $job): JsonResponse
    {
        $this->authorize('update', $job);

        $job = $this->jobService->update($job, $request->validated());

        return response()->json([
            'message' => 'Job listing updated.',
            'job'     => JobResource::make($job),
        ]);
    }

    public function destroy(Job $job): JsonResponse
    {
        $this->authorize('delete', $job);

        $this->jobService->delete($job);

        return response()->json(['message' => 'Job listing deleted.']);
    }

    public function myJobs(Request $request): JsonResponse
    {
        $jobs = $this->jobService->listForEmployer($request->user(), (int) $request->get('per_page', 15));

        return response()->json(JobResource::collection($jobs)->response()->getData(true));
    }
}
