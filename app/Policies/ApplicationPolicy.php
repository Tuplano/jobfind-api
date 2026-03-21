<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    /**
     * Only the employer who owns the job can update application status.
     */
    public function update(User $user, Application $application): bool
    {
        return $user->id === $application->jobListing->employer_id;
    }
}
