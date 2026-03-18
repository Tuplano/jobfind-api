<?php

namespace App\Enums;

enum ExperienceLevel: string
{
    case Entry  = 'entry';
    case Mid    = 'mid';
    case Senior = 'senior';
    case Lead   = 'lead';

    public function label(): string
    {
        return match($this) {
            self::Entry  => 'Entry Level',
            self::Mid    => 'Mid Level',
            self::Senior => 'Senior Level',
            self::Lead   => 'Lead / Principal',
        };
    }
}
