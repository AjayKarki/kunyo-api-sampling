<?php

namespace Foundation\Events;

use Foundation\Models\TicketEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketActivity
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TicketEvent
     */
    public $ticketEvent;
    /**
     * @var string
     */
    public $type;

    /**
     * Create a new event instance.
     *
     * @param TicketEvent $ticketEvent
     */
    public function __construct(TicketEvent $ticketEvent)
    {
        $this->ticketEvent = $ticketEvent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
