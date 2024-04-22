<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcastNow  // ShouldBroadcastNow =>for sync queue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $from;
    public $to;
    public $chatId;
    public $msgId;

//    public $connection = 'redis';
//
//
//    public $queue = 'default';

    public function __construct($from, $to, $message, $chatId, $msgId)
    {
        $this->message = $message;
        $this->from = $from;
        $this->to = $to;
        $this->chatId = $chatId;
        $this->msgId = $msgId;

//        $this->broadcastVia('pusher');
    }

    public function broadcastOn() //channel
    {
        return new Channel('support.' . $this->chatId);
        return new PrivateChannel('support.' . $this->chatId);
        return ['support'];
    }

    public function broadcastAs() //event on channel
    {
        return 'chat';
    }

    public function broadcastWith() //attributes in payload event
    {
//The data that is broadcasted can be set/overridden within the method broadcastWith
        return ['message' => $this->message, 'from' => $this->from, 'to' => $this->to, 'chatId' => $this->chatId, 'msgId' => $this->msgId];
    }

//    public function broadcastWhen()
//    {
//        return $this->order->value > 100;
//    }

//ChatEvent::dispatch($order);
//broadcast(new ChatEvent($update))->toOthers();

}
