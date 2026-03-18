<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'status'               => $this->status->value,
            'cover_letter'         => $this->cover_letter,
            'has_resume'           => ! is_null($this->resume_path),
            'resume_original_name' => $this->resume_original_name,
            'employer_notes'       => $this->when(
                $request->user()?->isEmployer(),
                $this->employer_notes
            ),
            'reviewed_at'          => $this->reviewed_at?->toISOString(),
            'job_listing'          => JobResource::make($this->whenLoaded('jobListing')),
            'employee'             => $this->whenLoaded('employee', fn () => [
                'id'        => $this->employee->id,
                'full_name' => $this->employee->full_name,
                'profile'   => EmployeeProfileResource::make($this->employee->employeeProfile),
            ]),
            'created_at'           => $this->created_at?->toISOString(),
        ];
    }
}
