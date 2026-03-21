<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $jobs = Job::with(['employer.employerProfile', 'skills'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(JobResource::collection($jobs)->response()->getData(true));
    }

    public function destroy(Job $job): JsonResponse
    {
        $job->delete();
        return response()->json(['message' => 'Job deleted.']);
    }
}
