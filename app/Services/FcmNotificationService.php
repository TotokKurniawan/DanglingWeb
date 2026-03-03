<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    /**
     * Kirim push notification ke satu user (semua device aktifnya).
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::getActiveTokens($userId);
        if (empty($tokens)) {
            return;
        }

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }

    /**
     * Kirim push notification ke satu device token via FCM HTTP v1 API.
     */
    protected function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $serverKey = config('services.fcm.server_key');
        if (empty($serverKey)) {
            Log::warning('FCM server key not configured. Skipping push notification.');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type'  => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data' => $data,
            ]);

            if ($response->failed()) {
                Log::error('FCM push failed', [
                    'token'    => substr($token, 0, 20) . '...',
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);

                // Jika token invalid, nonaktifkan
                if ($response->status() === 400 || str_contains($response->body(), 'NotRegistered')) {
                    DeviceToken::where('token', $token)->update(['is_active' => false]);
                }
            }
        } catch (\Exception $e) {
            Log::error('FCM push exception: ' . $e->getMessage());
        }
    }

    // ─── Helper methods untuk event-event spesifik ──────────────────────────

    /**
     * Notify buyer bahwa status order berubah.
     */
    public function notifyOrderStatusChanged(int $buyerUserId, int $orderId, string $newStatus): void
    {
        $messages = [
            'accepted'  => 'Order #%d diterima oleh seller! Pesanan sedang disiapkan.',
            'rejected'  => 'Order #%d ditolak oleh seller.',
            'completed' => 'Order #%d selesai! Terima kasih telah berbelanja.',
            'cancelled' => 'Order #%d dibatalkan.',
        ];

        $body = sprintf($messages[$newStatus] ?? 'Order #%d status berubah: %s', $orderId, $newStatus);

        $this->sendToUser($buyerUserId, 'Update Order', $body, [
            'type'     => 'order_status_changed',
            'order_id' => (string) $orderId,
            'status'   => $newStatus,
        ]);
    }

    /**
     * Notify seller bahwa ada order baru masuk.
     */
    public function notifyNewOrderForSeller(int $sellerUserId, int $orderId, string $buyerName): void
    {
        $body = sprintf('Order baru #%d dari %s. Segera konfirmasi!', $orderId, $buyerName);

        $this->sendToUser($sellerUserId, 'Order Baru!', $body, [
            'type'     => 'new_order',
            'order_id' => (string) $orderId,
        ]);
    }
}
