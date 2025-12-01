<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessagesRequested implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $chatId;
  public $messages; // Array of messages
  public $page;
  public $perPage;
  public $total;

  public function __construct($chatId, $messages, $page = 1, $perPage = 20, $total = 0)
  {
    $this->chatId = $chatId;
    $this->messages = $messages;
    $this->page = $page;
    $this->perPage = $perPage;
    $this->total = $total;
  }

  public function broadcastOn()
  {
    // قناة خاصة لكل محادثة
    return new PrivateChannel('chat.' . $this->chatId);
  }

  public function broadcastAs()
  {
    return 'chat.messages';
  }
}
