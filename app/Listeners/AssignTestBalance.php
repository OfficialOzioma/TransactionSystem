<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\UserBalance;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AssignTestBalance
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        // Set a default balance for the new user
        UserBalance::create([
            'user_id' => $event->user->id,
            'balance' => 1000.00,  // Set a test balance here
        ]);
    }
}
