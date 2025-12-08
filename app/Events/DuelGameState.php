<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelGameState implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $targetUserId;
    public $type; // 'answer', 'next', 'end' va h.k
    public $data;

    public function __construct($targetUserId, $type, $data)
    {
        $this->targetUserId = $targetUserId;
        $this->type = $type;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->targetUserId);
    }

    public function broadcastAs()
    {
        return 'DuelGameState';
    }
}
