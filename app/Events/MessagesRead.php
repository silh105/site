<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessagesRead implements ShouldBroadcast {
    use InteractsWithSockets;
    public function __construct(public int $chatId, public int$readerId) {}
    public function broadcastOn(): Channel { return new PrivateChannel('chat.' . $this->chatId); }
    public function broadcastAs(): string { return 'messages.read'; }
    public function broadcastWith(): array { return ['chat_id' => $this->chatId, 'reader_id' =>$this->readerId]; }
}