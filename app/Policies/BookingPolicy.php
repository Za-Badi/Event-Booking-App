<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * User can create a booking
     */
    public function create(User $user): bool
    {
        return true; // any authenticated user can book
    }

    /**
     * User can cancel their own booking
     */
    public function cancel(User $user, Booking $booking): bool
    {
        return $booking->user_id === $user->id;
    }
}
