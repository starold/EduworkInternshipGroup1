<?php

namespace App\Enums;

enum MotorStatus: string
{
    case Available = 'available';
    case Rented = 'rented';
    case Maintenance = 'maintenance';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match ($this) {
            MotorStatus::Available => 'Tersedia',
            MotorStatus::Rented => 'Disewa',
            MotorStatus::Maintenance => 'Perawatan',
            MotorStatus::Inactive => 'Tidak Aktif',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            MotorStatus::Available => 'success',
            MotorStatus::Rented => 'warning',
            MotorStatus::Maintenance => 'info',
            MotorStatus::Inactive => 'danger',
        };
    }
}
