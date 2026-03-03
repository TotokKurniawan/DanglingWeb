<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Setting;
use App\Services\FcmNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoCancelPendingOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Batalkan otomatis order pending yang sudah melewati batas waktu.';

    public function handle(): int
    {
        // Ambil timeout dari tabel settings, fallback ke config, fallback ke null (disabled)
        $minutes = Setting::getValue('order.buyer_cancel_timeout_minutes')
            ?? config('order.buyer_cancel_timeout_minutes');

        if (empty($minutes) || (int) $minutes <= 0) {
            $this->info('Auto-cancel disabled (timeout not configured).');
            return self::SUCCESS;
        }

        $cutoff = Carbon::now()->subMinutes((int) $minutes);

        $orders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No pending orders to auto-cancel.');
            return self::SUCCESS;
        }

        $count = 0;
        $fcm = app(FcmNotificationService::class);

        foreach ($orders as $order) {
            $order->update([
                'status'        => Order::STATUS_CANCELLED,
                'cancelled_by'  => 'system',
                'cancel_reason' => 'Otomatis dibatalkan — melewati batas waktu ' . $minutes . ' menit.',
            ]);

            ActivityLog::log('order.auto_cancelled', $order, [
                'timeout_minutes' => $minutes,
            ]);

            // Notify buyer
            try {
                $order->loadMissing('buyer');
                if ($order->buyer && $order->buyer->user_id) {
                    $fcm->notifyOrderStatusChanged($order->buyer->user_id, $order->id, 'cancelled');
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan proses
            }

            $count++;
        }

        $this->info("Auto-cancelled {$count} pending order(s).");
        return self::SUCCESS;
    }
}
