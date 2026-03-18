<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'phone'                => $this->phone,
            'dial_code'            => $this->dial_code,
            'city'                 => $this->city,
            'country'              => $this->country,
            'date_of_birth'        => $this->date_of_birth?->toDateString(),
            'linkedin_url'         => $this->linkedin_url,
            'portfolio_url'        => $this->portfolio_url,
            'about'                => $this->about,
            'has_resume'           => ! is_null($this->resume_path),
            'resume_original_name' => $this->resume_original_name,
            'is_open_to_work'      => $this->is_open_to_work,
            'setup_completed'      => $this->setup_completed,
            'skills'               => SkillResource::collection($this->whenLoaded('skills')),
            'updated_at'           => $this->updated_at?->toISOString(),
        ];
    }
}
