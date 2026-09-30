<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageDeleted implements ShouldBroadcast {
    use InteractsWithSockets;
    public function __construct(public int $chatId, public int$messageId) {}
    public function broadcastOn(): Channel { return new PrivateChannel('chat.' . $this->chatId); }
    public function broadcastAs(): string { return 'message.deleted'; }
    public function broadcastWith(): array { return ['message_id' => $this->messageId]; }
}