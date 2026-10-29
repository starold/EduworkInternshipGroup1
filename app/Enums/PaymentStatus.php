<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            PaymentStatus::Pending => 'Menunggu Pembayaran',
            PaymentStatus::Paid => 'Sudah Dibayar',
            PaymentStatus::Failed => 'Gagal',
            PaymentStatus::Refunded => 'Dikembalikan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            PaymentStatus::Pending => 'warning',
            PaymentStatus::Paid => 'success',
            PaymentStatus::Failed => 'danger',
            PaymentStatus::Refunded => 'info',
        };
    }
}
