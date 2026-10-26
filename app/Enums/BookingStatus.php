<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            BookingStatus::Pending => 'Menunggu',
            BookingStatus::Confirmed => 'Dikonfirmasi',
            BookingStatus::Rejected => 'Ditolak',
            BookingStatus::Cancelled => 'Dibatalkan',
            BookingStatus::Completed => 'Selesai',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            BookingStatus::Pending => 'warning',
            BookingStatus::Confirmed => 'success',
            BookingStatus::Rejected => 'danger',
            BookingStatus::Cancelled => 'secondary',
            BookingStatus::Completed => 'info',
        };
    }
}
