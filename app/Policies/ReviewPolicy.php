<?php

namespace App\Policies;

use App\Models\BookingItem;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, BookingItem $item): bool
    {
        return $item->booking->user_id === $user->id
            && $item->isReturned()
            && ! $item->hasUnpaidFines()
            && ! $item->review()->exists();
    }
}
