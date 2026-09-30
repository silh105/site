<?php
namespace App\Events;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast {
    use InteractsWithSockets, SerializesModels;
    public function __construct(public Message $message) {}
    public function broadcastOn(): Channel { return new PrivateChannel('chat.' . $this->message->chat_id); }
    public function broadcastAs(): string { return 'message.sent'; }
    public function broadcastWith(): array { return ['message' => $this->message->load(['sender.profile'])]; }
}