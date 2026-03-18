<?php

namespace App\Http\Requests\Jobs;

use App\Enums\ExperienceLevel;
use App\Enums\JobStatus;
use App\Enums\JobType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployer() ?? false;
    }

    public function rules(): array
    {
        return [
            'title'            => ['sometimes', 'string', 'max:255'],
            'description'      => ['sometimes', 'string', 'min:50'],
            'location'         => ['nullable', 'string', 'max:255'],
            'is_remote'        => ['nullable', 'boolean'],
            'type'             => ['sometimes', new Enum(JobType::class)],
            'experience_level' => ['sometimes', new Enum(ExperienceLevel::class)],
            'salary_min'       => ['nullable', 'integer', 'min:0'],
            'salary_max'       => ['nullable', 'integer', 'gte:salary_min'],
            'salary_currency'  => ['nullable', 'string', 'size:3'],
            'status'           => ['sometimes', new Enum(JobStatus::class)],
            'expires_at'       => ['nullable', 'date', 'after:today'],
            'skill_ids'        => ['nullable', 'array'],
            'skill_ids.*'      => ['integer', 'exists:skills,id'],
        ];
    }
}
