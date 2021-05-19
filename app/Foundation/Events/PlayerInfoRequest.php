<?php

namespace Foundation\Events;

use Foundation\Models\TicketEvent;
use Foundation\Models\TopUp;
use Foundation\Models\TopupPlayerInformation;
use Foundation\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerInfoRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var TopUp
     */
    public $topup;
    /**
     * @var User
     */
    public $user;
    public $orderId;

    /**
     * Create a new event instance.
     *
     * @param TopUp $topUp
     * @param User $user
     * @param $orderId
     */
    public function __construct(TopUp $topUp, User $user, $orderId)
    {
        $this->topup = $topUp;
        $this->user = $user;
        $this->orderId = $orderId;
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
