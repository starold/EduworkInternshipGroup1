<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case NotReturned = 'not_returned';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::NotReturned => 'Belum Kembali',
            self::Returned => 'Sudah Kembali',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::NotReturned => 'warning',
            self::Returned => 'success',
        };
    }
}
