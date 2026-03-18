<?php

use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Api\V1\Employer\ProfileController as EmployerProfileController;
use App\Http\Controllers\Api\V1\Jobs\ApplicationController;
use App\Http\Controllers\Api\V1\Jobs\JobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public ──────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login'])->middleware('throttle:5,1');

        // Email verification
        Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/resend', [VerifyEmailController::class, 'resend'])
            ->middleware(['auth:sanctum', 'throttle:6,1'])
            ->name('verification.send');
    });

    // Public job browsing
    Route::get('jobs',      [JobController::class, 'index']);
    Route::get('jobs/{job}', [JobController::class, 'show']);

    // ── Authenticated ────────────────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me',      [AuthController::class, 'me']);

        // Employee routes
        Route::prefix('employee')->middleware('role:employee')->group(function () {
            Route::get('profile',               [EmployeeProfileController::class, 'show']);
            Route::patch('profile',             [EmployeeProfileController::class, 'update']);
            Route::post('profile/resume',       [EmployeeProfileController::class, 'uploadResume']);
            Route::post('profile/setup-complete', [EmployeeProfileController::class, 'completeSetup']);

            Route::get('applications',          [ApplicationController::class, 'myApplications']);
            Route::post('jobs/{job}/apply',     [ApplicationController::class, 'apply']);
        });

        // Employer routes
        Route::prefix('employer')->middleware('role:employer')->group(function () {
            Route::get('profile',               [EmployerProfileController::class, 'show']);
            Route::patch('profile',             [EmployerProfileController::class, 'update']);
            Route::post('profile/logo',         [EmployerProfileController::class, 'uploadLogo']);
            Route::post('profile/setup-complete', [EmployerProfileController::class, 'completeSetup']);

            Route::get('jobs',                  [JobController::class, 'myJobs']);
            Route::post('jobs',                 [JobController::class, 'store']);
            Route::patch('jobs/{job}',          [JobController::class, 'update']);
            Route::delete('jobs/{job}',         [JobController::class, 'destroy']);

            Route::get('jobs/{job}/applications',             [ApplicationController::class, 'jobApplications']);
            Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);
        });

        // Admin routes
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::get('users',               [AdminUserController::class, 'index']);
            Route::get('users/{user}',        [AdminUserController::class, 'show']);
            Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);
            Route::delete('users/{user}',     [AdminUserController::class, 'destroy']);
        });
    });
});
