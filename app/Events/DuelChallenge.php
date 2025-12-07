<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
// ⚠️ ShouldBroadcastNow ga o'zgartiring
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelChallenge implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $challenger;
    public $targetUserId;
    public $quizId;
    public $subjectId;

    public function __construct($challenger, $targetUserId, $quizId, $subjectId)
    {
        $this->challenger = $challenger;
        $this->targetUserId = $targetUserId;
        $this->quizId = $quizId;
        $this->subjectId = $subjectId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->targetUserId);
    }
}
