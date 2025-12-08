<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $accepter;
    public $challenger;
    public $quizId;
    public $subjectId;

    public function __construct($accepter, $challenger, $quizId, $subjectId)
    {
        $this->accepter = $accepter;
        $this->challenger = $challenger;
        $this->quizId = $quizId;
        $this->subjectId = $subjectId;
    }

    public function broadcastOn()
    {
        // ✅ CHALLENGER (so'rov yuboruvchi) ga yuboriladi
        return new PrivateChannel('user.' . $this->challenger->id);
    }

    public function broadcastAs()
    {
        return 'DuelAccepted';
    }

    public function broadcastWith()
    {
        return [
            'accepter' => [
                'id' => $this->accepter->id,
                'first_name' => $this->accepter->first_name,
                'name' => $this->accepter->name,
                'avatar' => $this->accepter->avatar
            ],
            'quizId' => $this->quizId,
            'subjectId' => $this->subjectId
        ];
    }
}