<?php

namespace Foundation\Events;

use Foundation\Models\KYC;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KYCRespond
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var KYC
     */
    public $kyc;

    /**
     * Create a new event instance.
     *
     * @param KYC $kyc
     */
    public function __construct(KYC $kyc)
    {
        $this->kyc = $kyc;
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
