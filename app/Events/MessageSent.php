<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $messageData;
    public int $conversationId;

    public function __construct(Message $message, bool $isMine = false)
    {
        $this->conversationId = $message->conversation_id;
        $this->messageData = [
            'id'         => $message->id,
            'message'    => $message->message,
            'sender_id'  => $message->sender_id,
            'is_mine'    => $isMine,
            'is_read'    => $message->is_read,
            'created_at' => $message->created_at->toISOString(),
        ];
    }

    /**
     * Siaran ke private channel per percakapan.
     * Flutter akan subscribe ke: private-conversation.{conversationId}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("conversation.{$this->conversationId}"),
        ];
    }

    /**
     * Nama event yang diterima oleh Flutter (client).
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Data yang dikirim ke client.
     */
    public function broadcastWith(): array
    {
        return $this->messageData;
    }
}
