<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = ['name'];

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills', 'skill_id', 'job_listing_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeProfile::class, 'employee_skills');
    }
}
