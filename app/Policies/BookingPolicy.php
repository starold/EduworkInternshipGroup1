<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->user_id === $user->id;
    }

    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return ! in_array($booking->status, [BookingStatus::Completed, BookingStatus::Cancelled]);
        }

        return $booking->user_id === $user->id && $booking->isPending();
    }

    public function confirmReturn(User $user, Booking $booking): bool
    {
        return $user->isAdmin() && $booking->canBeReturned();
    }
}
