<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public array $payload;

  public function __construct(array $payload)
  {
    $this->payload = $payload;
  }

  /**
 *بترجع هون ل اي قناة فيا شات
 *  يعني   chat.4
 *  حسب ال id
   */
  public function broadcastOn()
  {
    return new PrivateChannel('chat.' . $this->payload['chat_id']);
  }
  /**
   * Summary of broadcastWith
   * @return array
   * هون شو بدك ترجع داتا
   */
  public function broadcastWith()
  {
    return $this->payload;
  }
// اسم ال ايفنت يلي لازم يستمعلو 
  public function broadcastAs()
  {
    return 'message.sent';
  }
}
