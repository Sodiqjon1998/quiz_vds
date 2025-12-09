<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelChallenge implements ShouldBroadcast
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
        // ✅ MUHIM: Private channel'ga yuborish
        return new PrivateChannel('user.' . $this->targetUserId);
    }

    public function broadcastAs()
    {
        // ✅ Event nomi (frontend'da .DuelChallenge deb tinglaydi)
        return 'DuelChallenge';
    }

    public function broadcastWith()
    {
        // ✅ Frontend'ga yuborilayotgan ma'lumotlar
        return [
            'challenger' => $this->challenger,
            'quizId' => $this->quizId,
            'subjectId' => $this->subjectId,
        ];
    }
}