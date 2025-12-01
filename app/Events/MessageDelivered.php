<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageDelivered implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public array $payload;

  public function __construct(array $payload)
  {
    $this->payload = $payload;
  }
  public function broadcastOn()
  {
    return new PrivateChannel('chat.' . $this->payload['chat_id']);
  }
  public function broadcastWith()
  {
    return $this->payload;
  }
  public function broadcastAs()
  {
    return 'message.delivered';
  }
}
