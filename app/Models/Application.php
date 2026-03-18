<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $fillable = [
        'job_listing_id',
        'employee_id',
        'status',
        'cover_letter',
        'resume_path',
        'resume_original_name',
        'employer_notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status'      => ApplicationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function jobListing(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_listing_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
