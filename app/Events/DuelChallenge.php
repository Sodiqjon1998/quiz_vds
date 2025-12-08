<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DuelChallenge implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $challenger;
    public $target;
    public $quizId;
    public $subjectId;

    public function __construct($challenger, $target, $quizId, $subjectId)
    {
        $this->challenger = $challenger;
        $this->target = $target;
        $this->quizId = $quizId;
        $this->subjectId = $subjectId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->target->id);
    }

    public function broadcastAs()
    {
        return 'DuelChallenge';
    }

    public function broadcastWith()
    {
        return [
            'challenger' => [
                'id' => $this->challenger->id,
                'first_name' => $this->challenger->first_name,
                'name' => $this->challenger->name,
                'avatar' => $this->challenger->avatar
            ],
            'quizId' => $this->quizId,  // ✅ TO'G'RI YUBORILADI
            'subjectId' => $this->subjectId  // ✅ TO'G'RI YUBORILADI
        ];
    }
}
