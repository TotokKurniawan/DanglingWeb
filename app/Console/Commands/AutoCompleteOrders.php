<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Setting;
use App\Services\FcmNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Selesaikan otomatis order accepted yang sudah melewati batas waktu.';

    public function handle(): int
    {
        $hours = Setting::getValue('order.auto_complete_hours');

        if (empty($hours) || (int) $hours <= 0) {
            $this->info('Auto-complete disabled (auto_complete_hours not configured).');
            return self::SUCCESS;
        }

        $cutoff = Carbon::now()->subHours((int) $hours);

        $orders = Order::where('status', Order::STATUS_ACCEPTED)
            ->where('accepted_at', '<=', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No accepted orders to auto-complete.');
            return self::SUCCESS;
        }

        $count = 0;
        $fcm = app(FcmNotificationService::class);

        foreach ($orders as $order) {
            $order->update([
                'status'       => Order::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);

            ActivityLog::log('order.auto_completed', $order, [
                'timeout_hours' => $hours,
            ]);

            // Notify buyer
            try {
                $order->loadMissing('buyer');
                if ($order->buyer && $order->buyer->user_id) {
                    $fcm->notifyOrderStatusChanged($order->buyer->user_id, $order->id, 'completed');
                }
            } catch (\Throwable $e) {
                // Jangan gagalkan proses
            }

            $count++;
        }

        $this->info("Auto-completed {$count} accepted order(s).");
        return self::SUCCESS;
    }
}
