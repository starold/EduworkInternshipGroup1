<?php

namespace App\Enums;

enum ConditionStatus: string
{
    case Good = 'good';
    case MinorDamage = 'minor_damage';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match($this) {
            ConditionStatus::Good => 'Baik',
            ConditionStatus::MinorDamage => 'Kerusakan Kecil',
            ConditionStatus::Damaged => 'Rusak',
        };
    }
}
