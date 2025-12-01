<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class ChatCreated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public array $payload;

  public function __construct(array $payload)
  {
    $this->payload = $payload;
  }

  public function broadcastOn()
  {
    // يبث على قناة الشخصين
    return [
      new PrivateChannel('private-user.' . $this->payload['user_id']),
      new PrivateChannel('private-user.' . $this->payload['admin_id']),
    ];
  }

  public function broadcastWith()
  {
    return $this->payload;
  }

  public function broadcastAs()
  {
    return 'chat.created';
  }
}
