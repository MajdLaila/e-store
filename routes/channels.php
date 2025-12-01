<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

// قناة خاصة لكل chat
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
  $chat = Chat::find($chatId);
  if (! $chat) return false;
  return $user->id === $chat->user_id || $user->id === $chat->admin_id;
});

// قناة خاصة للمستخدم (notifications, system messages)
Broadcast::channel('private-user.{userId}', function ($user, $userId) {
  return (int)$user->id === (int)$userId;
});
