<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
// ⚠️ MUHIM O'ZGARISH:
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <-- Buni qo'shing
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ⚠️ MUHIM O'ZGARISH:
class DuelAccepted implements ShouldBroadcastNow // <-- Buni o'zgartiring
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $accepter;
    public $challengerId;
    public $gameSessionId;

    public function __construct($accepter, $challengerId, $gameSessionId)
    {
        $this->accepter = $accepter;
        $this->challengerId = $challengerId;
        $this->gameSessionId = $gameSessionId;
    }

    public function broadcastOn()
    {
        // Xabar CHAQRUVCHI (Siz) ning kanaliga boradi
        return new PrivateChannel('user.' . $this->challengerId);
    }
}