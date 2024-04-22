<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Viewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $viewClass;
    public $userReward;
    public $isMeta;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $viewClass, $userReward = false, $isMeta = false)
    {
        $this->viewClass = $viewClass;
        $this->isMeta = $isMeta;
        $this->data = $data;
        $this->userReward = $userReward;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-viewed');
    }
}
