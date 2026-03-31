<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Models\Message
     */
    public $message;

    /**
     * Create a new event instance.
     * 
     * @param \App\Models\Message $message
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return $this->message->conversation->participants()
            ->where('users.id', '<>', $this->message->user_id)
            ->pluck('users.id')
            ->map(function ($userId) {
                return new PresenceChannel('Messenger.' . $userId);
            })
            ->all();
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
