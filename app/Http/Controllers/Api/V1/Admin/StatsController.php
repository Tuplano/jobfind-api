<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'total_users'        => User::count(),
            'total_employees'    => User::where('role', 'employee')->count(),
            'total_employers'    => User::where('role', 'employer')->count(),
            'total_jobs'         => Job::count(),
            'active_jobs'        => Job::where('status', 'active')->count(),
            'total_applications' => Application::count(),
        ]);
    }
}
