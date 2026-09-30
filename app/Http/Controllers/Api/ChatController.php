<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\MessageDeleted;
use App\Events\MessagesRead;

class ChatController extends Controller {
    public function index(Request $request) {
        $userId = $request->user()->id;
        $chats = Chat::whereHas('users', fn($q) => $q->where('users.id', $userId))
            ->with([
                'users' => fn($q) => $q->where('users.id', '!=', $userId)->with('profile'),
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->get()
            ->map(function ($chat) use ($userId) {
                $companion = $chat->users->first();
                $lastMsg = $chat->messages->first();
                $unread = Message::where('chat_id', $chat->id)
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)->count();

                return [
                    'id' => $chat->id,
                    'companion' => $companion ? [
                        'id' => $companion->id,
                        'name' => $companion->profile->full_name ?? $companion->login,
                        'avatar_url' => $companion->profile->avatar_url ?? null,
                    ] : null,
                    'last_message' => $lastMsg ? [
                        'text' => $lastMsg->text,
                        'created_at' => $lastMsg->created_at->format('H:i'),
                        'is_read' => $lastMsg->is_read,
                        'is_mine' => $lastMsg->sender_id ===$userId,
                    ] : null,
                    'unread_count' => $unread,
                ];
            });

        return response()->json($chats);
    }

    public function show(Request $request, Chat$chat) {
        abort_unless($chat->users()->where('users.id',$request->user()->id)->exists(), 403);
        return response()->json($chat->messages()->with('sender.profile')->orderBy('created_at', 'asc')->get());
    }

    public function sendMessage(Request $request, Chat$chat) {
        $userId =$request->user()->id;
        abort_unless($chat->users()->where('users.id', $userId)->exists(), 403);$request->validate(['text' => 'required|string|max:4000']);

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $userId,
            'text' => $request->text,
            'is_read' => false
        ]);

        broadcast(new MessageSent($message))->toOthers();
        return response()->json($message->load('sender.profile'), 201);
    }

    public function markAsRead(Request $request, Chat$chat) {
        $userId =$request->user()->id;
        abort_unless($chat->users()->where('users.id',$userId)->exists(), 403);
        $updated = Message::where('chat_id',$chat->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($updated > 0) broadcast(new MessagesRead($chat->id,$userId))->toOthers();
        return response()->json(['success' => true]);
    }

    public function deleteMessage(Request $request, Message$message) {
        $userId =$request->user()->id;
        if ($message->sender_id !== $userId && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Запрещено'], 403);
        }
        $chatId =$message->chat_id;
        $msgId =$message->id;
        $message->delete();
        broadcast(new MessageDeleted($chatId,$msgId))->toOthers();
        return response()->json(['success' => true]);
    }
}