<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'dial_code',
        'city',
        'country',
        'date_of_birth',
        'linkedin_url',
        'portfolio_url',
        'about',
        'resume_path',
        'resume_original_name',
        'profile_photo_path',
        'is_open_to_work',
        'setup_completed',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'    => 'date',
            'is_open_to_work'  => 'boolean',
            'setup_completed'  => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'employee_skills');
    }
}
