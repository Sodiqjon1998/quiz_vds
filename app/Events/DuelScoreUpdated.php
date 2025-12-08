<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <-- Tezkor bo'lishi uchun
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelScoreUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;      // Ball olgan o'yinchining IDsi
    public $newScore;    // Yangi ball
    public $opponentId;  // Raqib IDsi (xabar shunga boradi)

    public function __construct($userId, $newScore, $opponentId)
    {
        $this->userId = $userId;
        $this->newScore = $newScore;
        $this->opponentId = $opponentId;
    }

    public function broadcastOn()
    {
        // Xabar raqibning shaxsiy kanaliga boradi
        return new PrivateChannel('user.' . $this->opponentId);
    }

    public function broadcastAs()
    {
        return 'DuelScoreUpdated';
    }
}
