<?php

// app/Events/MessageSent.php
// Yaratish: php artisan make:event MessageSent

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $username;
    public $message;

    /**
     * Yangi event instance yaratish
     */
    public function __construct($username, $message)
    {
        $this->username = $username;
        $this->message = $message;
    }

    /**
     * Event qaysi kanalga broadcast qilinadi
     */
    public function broadcastOn()
    {
        // Public channel - hamma ko'radi
        return new Channel('chat');
        
        // Private channel uchun:
        // return new PrivateChannel('chat');
        
        // Presence channel uchun (kim online ekanini bilish):
        // return new PresenceChannel('chat');
    }

    /**
     * Event nomi (ixtiyoriy)
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }

    /**
     * Qanday ma'lumot yuboriladi
     */
    public function broadcastWith()
    {
        return [
            'username' => $this->username,
            'message' => $this->message,
            'time' => now()->format('H:i'),
        ];
    }
}