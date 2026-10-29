<?php

namespace App\Enums;

enum RentalStatus: string
{
    case Scheduled = 'scheduled';
    case Active = 'active';
    case Returned = 'returned';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            RentalStatus::Scheduled => 'Terjadwal',
            RentalStatus::Active => 'Aktif',
            RentalStatus::Returned => 'Dikembalikan',
            RentalStatus::Completed => 'Selesai',
        };
    }
}
