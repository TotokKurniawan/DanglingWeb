<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\FcmNotificationService;
use Illuminate\Http\Request;


class ChatController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/chat — list percakapan user saat ini.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $buyerId  = $user->buyer->id ?? null;
        $sellerId = $user->seller->id ?? null;

        $conversations = Conversation::with(['buyer.user', 'seller.user', 'latestMessage'])
            ->where(function ($q) use ($buyerId, $sellerId) {
                if ($buyerId) {
                    $q->orWhere('buyer_id', $buyerId);
                }
                if ($sellerId) {
                    $q->orWhere('seller_id', $sellerId);
                }
            })
            ->get()
            ->sortByDesc(fn ($c) => $c->latestMessage->created_at ?? $c->created_at)
            ->values();

        $formatted = $conversations->map(function ($c) use ($user) {
            $isBuyerSide = $c->buyer->user_id === $user->id;
            $partner = $isBuyerSide ? $c->seller->user : $c->buyer->user;

            $unreadCount = $c->messages()
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->count();

            return [
                'id'              => $c->id,
                'partner_id'      => $partner->id,
                'partner_name'    => $isBuyerSide ? $c->seller->store_name : $partner->name,
                'partner_photo'   => $partner->photo_path ? url('storage/' . $partner->photo_path) : null,
                'latest_message'  => $c->latestMessage ? [
                    'message'    => $c->latestMessage->message,
                    'created_at' => $c->latestMessage->created_at,
                    'is_read'    => $c->latestMessage->is_read,
                    'is_mine'    => $c->latestMessage->sender_id === $user->id,
                ] : null,
                'unread_count'    => $unreadCount,
            ];
        });

        return $this->success(['conversations' => $formatted], 'Success', 200);
    }

    /**
     * GET /api/chat/{id} — list pesan dalam satu percakapan.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $buyerId  = $user->buyer->id ?? null;
        $sellerId = $user->seller->id ?? null;

        $conversation = Conversation::find($id);

        if (! $conversation) {
            return $this->error('Conversation not found', 404);
        }

        if (($buyerId && $conversation->buyer_id === $buyerId) || ($sellerId && $conversation->seller_id === $sellerId)) {
            // Authorized
            // Tandai pesan sebagai dibaca
            Message::where('conversation_id', $id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->map(function ($m) use ($user) {
                    return [
                        'id'         => $m->id,
                        'message'    => $m->message,
                        'is_mine'    => $m->sender_id === $user->id,
                        'is_read'    => $m->is_read,
                        'created_at' => $m->created_at,
                    ];
                });

            return $this->success([
                'conversation_id' => $conversation->id,
                'messages'        => $messages,
            ], 'Success', 200);
        }

        return $this->error('Forbidden', 403);
    }

    /**
     * POST /api/chat — mulai percakapan baru atau ambil yang sudah ada berdasarkan user_id.
     */
    public function storeConversation(Request $request)
    {
        $request->validate([
            'partner_user_id' => 'required|exists:users,id',
        ]);

        $user = $request->user();
        $targetUser = User::with(['buyer', 'seller'])->find($request->partner_user_id);

        // Cari tahu peran: siapa buyer, siapa seller
        // (Asumsi sederhana: jika user punya buyer dan target punya seller -> chat sebagai buyer ke seller)
        // Jika user punya seller dan target punya buyer -> chat sebagai seller ke buyer
        $buyer = null;
        $seller = null;

        if ($user->buyer && $targetUser->seller) {
            $buyer = $user->buyer;
            $seller = $targetUser->seller;
        } elseif ($user->seller && $targetUser->buyer) {
            $seller = $user->seller;
            $buyer = $targetUser->buyer;
        }

        if (! $buyer || ! $seller) {
            return $this->error('Percakapan harus antara buyer dan seller.', 422);
        }

        $conversation = Conversation::firstOrCreate([
            'buyer_id'  => $buyer->id,
            'seller_id' => $seller->id,
        ]);

        return $this->success(['conversation_id' => $conversation->id], 'Conversation started', 201);
    }

    /**
     * POST /api/chat/{id} — kirim pesan.
     */
    public function sendMessage(Request $request, $id, FcmNotificationService $fcmService)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = $request->user();
        $buyerId  = $user->buyer->id ?? null;
        $sellerId = $user->seller->id ?? null;

        $conversation = Conversation::with(['buyer.user', 'seller.user'])->find($id);

        if (! $conversation) {
            return $this->error('Conversation not found', 404);
        }

        if (($buyerId && $conversation->buyer_id === $buyerId) || ($sellerId && $conversation->seller_id === $sellerId)) {
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'message'         => $request->message,
            ]);

            // Broadcast ke semua subscriber channel real-time
            broadcast(new MessageSent($message))->toOthers();

            // Target notifikasi
            $isBuyerSide = $conversation->buyer->user_id === $user->id;
            $targetUserId = $isBuyerSide ? $conversation->seller->user_id : $conversation->buyer->user_id;

            // Sender name (store_name jika seller, user name jika buyer)
            $senderName = $isBuyerSide ? $user->name : ($user->seller->store_name ?? $user->name);

            // Kirim notifikasi push + in-app
            $fcmService->sendToUser($targetUserId, "Pesan baru dari {$senderName}", $request->message, [
                'type'            => 'new_chat_message',
                'conversation_id' => (string) $conversation->id,
            ]);
            \App\Models\UserNotification::send($targetUserId, "Pesan baru dari {$senderName}", $request->message, 'chat', [
                'conversation_id' => $conversation->id,
            ]);

            return $this->success([
                'id'         => $message->id,
                'message'    => $message->message,
                'is_mine'    => true,
                'is_read'    => false,
                'created_at' => $message->created_at,
            ], 'Message sent', 201);
        }

        return $this->error('Forbidden', 403);
    }
}
