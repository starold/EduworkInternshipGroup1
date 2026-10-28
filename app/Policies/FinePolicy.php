<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    public function view(User $user, Fine $fine): bool
    {
        return $user->isAdmin() || $fine->bookingItem->booking->user_id === $user->id;
    }

    public function pay(User $user, Fine $fine): bool
    {
        return $fine->bookingItem->booking->user_id === $user->id && $fine->isUnpaid();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}
