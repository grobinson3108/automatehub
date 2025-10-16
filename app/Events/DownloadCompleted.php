<?php

namespace App\Events;

use App\Models\User;
use App\Models\Tutorial;
use App\Models\Download;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DownloadCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $tutorial;
    public $download;
    public $fileName;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Tutorial $tutorial, Download $download, string $fileName)
    {
        $this->user = $user;
        $this->tutorial = $tutorial;
        $this->download = $download;
        $this->fileName = $fileName;
    }
}
