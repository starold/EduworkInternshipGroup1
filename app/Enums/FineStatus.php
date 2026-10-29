<?php

namespace App\Enums;

enum FineStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            FineStatus::Unpaid => 'Belum Dibayar',
            FineStatus::Paid => 'Sudah Dibayar',
        };
    }
}
