<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'location'         => $this->location,
            'is_remote'        => $this->is_remote,
            'type'             => $this->type->value,
            'type_label'       => $this->type->label(),
            'experience_level' => $this->experience_level->value,
            'experience_label' => $this->experience_level->label(),
            'salary_min'       => $this->salary_min,
            'salary_max'       => $this->salary_max,
            'salary_currency'  => $this->salary_currency,
            'status'           => $this->status->value,
            'expires_at'       => $this->expires_at?->toISOString(),
            'skills'           => SkillResource::collection($this->whenLoaded('skills')),
            'employer'         => $this->whenLoaded('employer', fn () => [
                'id'           => $this->employer->id,
                'company_name' => $this->employer->employerProfile?->company_name,
                'logo'         => $this->employer->employerProfile?->logo_path,
                'city'         => $this->employer->employerProfile?->city,
                'country'      => $this->employer->employerProfile?->country,
            ]),
            'applications_count' => $this->whenCounted('applications'),
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
