<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
        'company_size',
        'founded_year',
        'description',
        'website',
        'contact_email',
        'contact_phone',
        'dial_code',
        'address',
        'city',
        'country',
        'logo_path',
        'setup_completed',
    ];

    protected function casts(): array
    {
        return [
            'setup_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
