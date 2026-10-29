<?php

namespace App\Enums;

enum FineType: string
{
    case Late = 'late';
    case Damage = 'damage';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            FineType::Late => 'Keterlambatan',
            FineType::Damage => 'Kerusakan',
            FineType::Other => 'Lainnya',
        };
    }
}
