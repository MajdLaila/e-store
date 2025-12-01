<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Events\ChatCreated;
use App\Events\MessageSent;
use App\Events\MessageEdited;
use App\Events\MessageDeleted;
use App\Events\MessageDelivered;
use App\Events\TypingStatus;
use App\Events\ChatMessagesRequested;
use App\Events\UserOnline;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
  public function __construct()
  {
    // تأكد المسارات محمية بالمصادقة
    $this->middleware('auth:sanctum'); // أو 'auth' حسب إعدادك
  }

  // 1. أنشئ محادثة جديدة
  public function createChat(Request $request)
  {
    $data = $request->validate([
      'admin_id' => 'nullable|exists:users,id',
    ]);

    $chat = Chat::create([
      'user_id' => $request->user()->id,
      'admin_id' => $data['admin_id'] ?? null,
      'status' => 'open',
      'last_message_at' => now(),
    ]);

    $payload = [
      'chat_id' => $chat->id,
      'user_id' => $chat->user_id,
      'admin_id' => $chat->admin_id,
      'status' => $chat->status,
      'created_at' => $chat->created_at->toDateTimeString(),
    ];

    event(new ChatCreated($payload));

    return response()->json(['status' => 'ok', 'chat' => $chat], 201);
  }

  // 2. جلب رسائل المحادثة (pagination)
  public function messages(Request $request, $chatId)
  {
    $request->validate([
      'page' => 'integer|min:1',
      'per_page' => 'integer|min:1|max:100',
    ]);

    $page = (int) $request->input('page', 1);
    $perPage = (int) $request->input('per_page', 20);

    $chat = Chat::findOrFail($chatId);
    $this->authorizeAccessToChat($request->user(), $chat);

    $query = Message::where('chat_id', $chatId)->orderBy('created_at', 'desc');

    $total = $query->count();
    $messages = $query->skip(($page - 1) * $perPage)->take($perPage)->get()->reverse()->values();

    // ابعث حدث طلب الرسائل (اختياري، تستطيع حذف هذا السطر)
    event(new ChatMessagesRequested($chatId, $messages->toArray(), $page, $perPage, $total));

    return response()->json([
      'status' => 'ok',
      'data' => $messages,
      'meta' => compact('page', 'perPage', 'total'),
    ]);
  }

  // 3. إرسال رسالة
  public function sendMessage(Request $request, $chatId)
  {
    $data = $request->validate([
      'message' => 'nullable|string',
      'type' => 'nullable|string|in:text,image,file,system',
      'metadata' => 'nullable|array',
    ]);

    $chat = Chat::findOrFail($chatId);
    $this->authorizeAccessToChat($request->user(), $chat);

    DB::beginTransaction();
    try {
      $message = Message::create([
        'chat_id' => $chatId,
        'sender_id' => $request->user()->id,
        'message' => $data['message'] ?? null,
        'type' => $data['type'] ?? 'text',
        'seen' => false,
        'created_at' => now(),
      ]);

      $chat->update(['last_message_at' => now()]);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();
      return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }

    $payload = [
      'chat_id' => $chatId,
      'message_id' => $message->id,
      'sender_id' => $message->sender_id,
      'message' => $message->message,
      'type' => $message->type,
      'created_at' => $message->created_at->toDateTimeString(),
      'status' => 'sent',
      'metadata' => $data['metadata'] ?? null,
    ];

    // استخدم toOthers() ليمنع إعادة الحدث إلى نفس socket (Laravel يقرأ X-Socket-Id header)
    broadcast(new MessageSent($payload))->toOthers();

    return response()->json(['status' => 'ok', 'message' => $payload], 201);
  }

  // 4. تعديل رسالة
  public function editMessage(Request $request, $messageId)
  {
    $data = $request->validate(['message' => 'required|string']);
    $message = Message::findOrFail($messageId);

    // فقط المرسل يمكنه التعديل (عدل حسب سياساتك)
    if ($message->sender_id !== $request->user()->id) {
      return response()->json(['status' => 'forbidden'], 403);
    }

    $message->update(['message' => $data['message']]);

    $payload = [
      'chat_id' => $message->chat_id,
      'message_id' => $message->id,
      'editor_id' => $request->user()->id,
      'message' => $message->message,
      'updated_at' => $message->updated_at->toDateTimeString(),
    ];

    event(new MessageEdited($payload));

    return response()->json(['status' => 'ok']);
  }

  // 5. حذف رسالة
  public function deleteMessage(Request $request, $messageId)
  {
    $message = Message::findOrFail($messageId);

    if ($message->sender_id !== $request->user()->id) {
      return response()->json(['status' => 'forbidden'], 403);
    }

    $chatId = $message->chat_id;
    $message->delete();

    event(new MessageDeleted([
      'chat_id' => $chatId,
      'message_id' => $messageId,
      'deleter_id' => $request->user()->id,
    ]));

    return response()->json(['status' => 'ok']);
  }

  // 6. وضع قراءة / توصيل
  public function markDelivered(Request $request, $messageId)
  {
    $message = Message::findOrFail($messageId);
    $message->update(['seen' => true, 'delivered_at' => now()]);

    event(new MessageDelivered([
      'chat_id' => $message->chat_id,
      'message_id' => $message->id,
      'delivered_to' => $request->user()->id,
      'delivered_at' => now()->toDateTimeString(),
    ]));

    return response()->json(['status' => 'ok']);
  }

  // 7. typing status (يفضّل أن يكون الحدث ShouldBroadcastNow)
  public function typing(Request $request, $chatId)
  {
    $data = $request->validate(['is_typing' => 'required|boolean']);
    $chat = Chat::findOrFail($chatId);
    $this->authorizeAccessToChat($request->user(), $chat);

    $payload = [
      'chat_id' => $chatId,
      'user_id' => $request->user()->id,
      'is_typing' => (bool) $data['is_typing'],
    ];

    // يفضّل TypingStatus implements ShouldBroadcastNow
    event(new TypingStatus($payload));

    return response()->json(['status' => 'ok']);
  }

  // 8. Set user online (يمكن استدعاؤها عند login)
  public function setOnline(Request $request, $chatId = null)
  {
    $payload = [
      'chat_id' => $chatId, // اختياري
      'user_id' => $request->user()->id,
      'online' => true,
    ];

    event(new UserOnline($payload));

    return response()->json(['status' => 'ok']);
  }

  // Utility: تحقق أن المستخدم طرف في الشات
  protected function authorizeAccessToChat($user, Chat $chat)
  {
    if ($user->id !== $chat->user_id && $user->id !== $chat->admin_id) {
      abort(403, 'Not a participant of the chat.');
    }
  }
}
