<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'company_name'    => $this->company_name,
            'industry'        => $this->industry,
            'company_size'    => $this->company_size,
            'founded_year'    => $this->founded_year,
            'description'     => $this->description,
            'website'         => $this->website,
            'contact_email'   => $this->contact_email,
            'contact_phone'   => $this->contact_phone,
            'dial_code'       => $this->dial_code,
            'address'         => $this->address,
            'city'            => $this->city,
            'country'         => $this->country,
            'has_logo'        => ! is_null($this->logo_path),
            'setup_completed' => $this->setup_completed,
            'updated_at'      => $this->updated_at?->toISOString(),
        ];
    }
}
