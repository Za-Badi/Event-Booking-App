<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{
    /**
     * Admin can manage events
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->role === 'admin';
    }
}
