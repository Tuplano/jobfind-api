<?php

namespace App\Enums;

enum UserRole: string
{
    case Employee = 'employee';
    case Employer = 'employer';
    case Admin    = 'admin';
}
