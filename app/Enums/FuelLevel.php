<?php

namespace App\Enums;

enum FuelLevel: string
{
    case Empty = 'empty';
    case Quarter = 'quarter';
    case Half = 'half';
    case ThreeQuarter = 'three_quarter';
    case Full = 'full';

    public function label(): string
    {
        return match ($this) {
            FuelLevel::Empty => 'Kosong',
            FuelLevel::Quarter => '1/4',
            FuelLevel::Half => '1/2',
            FuelLevel::ThreeQuarter => '3/4',
            FuelLevel::Full => 'Penuh',
        };
    }
}
