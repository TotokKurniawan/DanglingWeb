<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/notifications — list notifikasi user (terbaru dulu).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $perPage = min((int) $request->input('per_page', 20), 50);

        $notifications = UserNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return $this->success([
            'notifications' => $notifications->items(),
            'pagination'    => [
                'current_page' => $notifications->currentPage(),
                'last_page'    => $notifications->lastPage(),
                'total'        => $notifications->total(),
            ],
        ], 'Success', 200);
    }

    /**
     * PUT /api/notifications/{id}/read — tandai notifikasi sebagai dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = UserNotification::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (! $notification) {
            return $this->error('Notification not found', 404);
        }

        $notification->update(['is_read' => true]);

        return $this->success([], 'Notification marked as read', 200);
    }

    /**
     * GET /api/notifications/unread-count — jumlah notifikasi belum dibaca.
     */
    public function unreadCount(Request $request)
    {
        $count = UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return $this->success(['unread_count' => $count], 'Success', 200);
    }
}
