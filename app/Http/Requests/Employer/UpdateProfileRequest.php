<?php

namespace App\Http\Requests\Employer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployer() ?? false;
    }

    public function rules(): array
    {
        return [
            'company_name'  => ['sometimes', 'required', 'string', 'max:255'],
            'industry'      => ['nullable', 'string', 'max:100'],
            'company_size'  => ['nullable', 'string', 'max:50'],
            'founded_year'  => ['nullable', 'integer', 'min:1800', 'max:' . date('Y')],
            'description'   => ['nullable', 'string', 'max:3000'],
            'website'       => ['nullable', 'url', 'max:500'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'dial_code'     => ['nullable', 'string', 'max:10'],
            'address'       => ['nullable', 'string', 'max:500'],
            'city'          => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
        ];
    }
}
