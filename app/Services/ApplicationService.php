<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ApplicationService
{
    public function apply(User $employee, Job $job, array $data): Application
    {
        $exists = Application::where('job_listing_id', $job->id)
            ->where('employee_id', $employee->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'job' => ['You have already applied to this job.'],
            ]);
        }

        $resumePath = null;
        $resumeOriginalName = null;

        if (isset($data['resume'])) {
            /** @var UploadedFile $file */
            $file = $data['resume'];
            $resumePath = $file->store("applications/{$employee->id}", 'local');
            $resumeOriginalName = $file->getClientOriginalName();
            unset($data['resume']);
        }

        return Application::create([
            'job_listing_id'       => $job->id,
            'employee_id'          => $employee->id,
            'cover_letter'         => $data['cover_letter'] ?? null,
            'resume_path'          => $resumePath,
            'resume_original_name' => $resumeOriginalName,
        ]);
    }

    public function updateStatus(Application $application, string $status, ?string $notes = null): Application
    {
        $application->update([
            'status'         => $status,
            'employer_notes' => $notes,
            'reviewed_at'    => now(),
        ]);

        return $application->fresh();
    }

    public function getForEmployee(User $employee, int $perPage)
    {
        return Application::with(['jobListing.employer.employerProfile'])
            ->where('employee_id', $employee->id)
            ->latest()
            ->paginate($perPage);
    }

    public function getForJob(Job $job, int $perPage)
    {
        return Application::with(['employee.employeeProfile'])
            ->where('job_listing_id', $job->id)
            ->latest()
            ->paginate($perPage);
    }
}
