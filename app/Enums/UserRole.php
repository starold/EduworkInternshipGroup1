<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Admin = 'admin';
    case Owner = 'owner';

    public function label(): string
    {
        return match($this) {
            UserRole::Customer => 'Customer',
            UserRole::Admin => 'Admin',
            UserRole::Owner => 'Owner',
        };
    }
}
