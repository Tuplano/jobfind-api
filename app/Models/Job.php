<?php

namespace App\Models;

use App\Enums\JobStatus;
use App\Enums\JobType;
use App\Enums\ExperienceLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'job_listings';

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'location',
        'is_remote',
        'type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_remote'        => 'boolean',
            'type'             => JobType::class,
            'experience_level' => ExperienceLevel::class,
            'status'           => JobStatus::class,
            'expires_at'       => 'datetime',
        ];
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_listing_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_listing_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', JobStatus::Active);
    }
}
