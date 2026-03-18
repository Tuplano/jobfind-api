<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isEmployee() ?? false;
    }

    public function rules(): array
    {
        return [
            'phone'         => ['nullable', 'string', 'max:20'],
            'dial_code'     => ['nullable', 'string', 'max:10'],
            'city'          => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:-16 years'],
            'linkedin_url'  => ['nullable', 'url', 'max:500'],
            'portfolio_url' => ['nullable', 'url', 'max:500'],
            'about'         => ['nullable', 'string', 'max:2000'],
            'is_open_to_work' => ['nullable', 'boolean'],
            'skills'        => ['nullable', 'array'],
            'skills.*'      => ['integer', 'exists:skills,id'],
        ];
    }
}
