<?php

namespace App\Enums;

enum PickupMethod: string
{
    case Pickup = 'pickup';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            PickupMethod::Pickup => 'Ambil di Tempat',
            PickupMethod::Delivery => 'Diantar',
        };
    }
}
