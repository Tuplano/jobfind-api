<?php

namespace App\Services;

use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmployeeService
{
    public function getOrCreateProfile(User $user): EmployeeProfile
    {
        return $user->employeeProfile ?? EmployeeProfile::create(['user_id' => $user->id]);
    }

    public function updateProfile(User $user, array $data): EmployeeProfile
    {
        $profile = $this->getOrCreateProfile($user);

        if (isset($data['skills'])) {
            $skillIds = $data['skills'];
            unset($data['skills']);
            $profile->skills()->sync($skillIds);
        }

        $profile->update($data);

        return $profile->fresh('skills');
    }

    public function uploadResume(User $user, UploadedFile $file): array
    {
        $profile = $this->getOrCreateProfile($user);

        if ($profile->resume_path) {
            Storage::disk('local')->delete($profile->resume_path);
        }

        $path = $file->store("resumes/{$user->id}", 'local');

        $profile->update([
            'resume_path'          => $path,
            'resume_original_name' => $file->getClientOriginalName(),
        ]);

        return [
            'resume_path'          => $path,
            'resume_original_name' => $file->getClientOriginalName(),
        ];
    }

    public function markSetupComplete(User $user): EmployeeProfile
    {
        $profile = $this->getOrCreateProfile($user);
        $profile->update(['setup_completed' => true]);

        return $profile;
    }
}
