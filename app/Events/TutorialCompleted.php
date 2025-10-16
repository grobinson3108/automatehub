<?php

namespace App\Events;

use App\Models\User;
use App\Models\Tutorial;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TutorialCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $tutorial;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Tutorial $tutorial)
    {
        $this->user = $user;
        $this->tutorial = $tutorial;
    }
}
