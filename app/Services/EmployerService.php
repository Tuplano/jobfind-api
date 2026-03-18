<?php

namespace App\Services;

use App\Models\EmployerProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmployerService
{
    public function getOrCreateProfile(User $user): EmployerProfile
    {
        return $user->employerProfile ?? EmployerProfile::create(['user_id' => $user->id, 'company_name' => '']);
    }

    public function updateProfile(User $user, array $data): EmployerProfile
    {
        $profile = $this->getOrCreateProfile($user);
        $profile->update($data);

        return $profile->fresh();
    }

    public function uploadLogo(User $user, UploadedFile $file): array
    {
        $profile = $this->getOrCreateProfile($user);

        if ($profile->logo_path) {
            Storage::disk('local')->delete($profile->logo_path);
        }

        $path = $file->store("logos/{$user->id}", 'local');

        $profile->update(['logo_path' => $path]);

        return ['logo_path' => $path];
    }

    public function markSetupComplete(User $user): EmployerProfile
    {
        $profile = $this->getOrCreateProfile($user);
        $profile->update(['setup_completed' => true]);

        return $profile;
    }
}
