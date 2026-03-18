<?php

namespace App\Services;

use App\Enums\JobStatus;
use App\Models\Job;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class JobService
{
    public function listActive(array $filters = []): LengthAwarePaginator
    {
        $query = Job::with(['employer.employerProfile', 'skills'])
            ->active();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['experience_level'])) {
            $query->where('experience_level', $filters['experience_level']);
        }

        if (! empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (isset($filters['is_remote'])) {
            $query->where('is_remote', $filters['is_remote']);
        }

        return $query->latest()->paginate(15);
    }

    public function listForEmployer(User $employer): Collection
    {
        return Job::with(['skills', 'applications'])
            ->where('employer_id', $employer->id)
            ->latest()
            ->get();
    }

    public function create(User $employer, array $data): Job
    {
        $skillIds = $data['skill_ids'] ?? [];
        unset($data['skill_ids']);

        $job = Job::create(array_merge($data, ['employer_id' => $employer->id]));

        if ($skillIds) {
            $job->skills()->sync($skillIds);
        }

        return $job->load('skills');
    }

    public function update(Job $job, array $data): Job
    {
        if (isset($data['skill_ids'])) {
            $job->skills()->sync($data['skill_ids']);
            unset($data['skill_ids']);
        }

        $job->update($data);

        return $job->fresh('skills');
    }

    public function delete(Job $job): void
    {
        $job->delete();
    }
}
