<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Private channel: conversation.{conversationId}
 * Hanya buyer atau seller dari percakapan yang dapat subscribe.
 */
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);

    if (!$conversation) return false;

    $buyerUserId  = $conversation->buyer?->user_id;
    $sellerUserId = $conversation->seller?->user_id;

    return $user->id === $buyerUserId || $user->id === $sellerUserId;
});

