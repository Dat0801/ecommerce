<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoUpdateOrderStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(OrderService $orderService): void
    {
        // Auto-complete shipped orders after 7 days
        $shippedOrders = Order::where('status', 'shipped')
            ->where('shipped_at', '<=', now()->subDays(7))
            ->whereDoesntHave('returns', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->get();

        foreach ($shippedOrders as $order) {
            try {
                $orderService->updateStatus(
                    $order->id,
                    'completed',
                    null,
                    'Automatically completed after 7 days of shipping',
                    null // System change
                );
                Log::info('Auto-completed order', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Failed to auto-complete order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Auto-cancel pending orders after 3 days (if not paid)
        $pendingOrders = Order::where('status', 'pending')
            ->where('payment_status', '!=', 'paid')
            ->where('created_at', '<=', now()->subDays(3))
            ->get();

        foreach ($pendingOrders as $order) {
            try {
                $orderService->updateStatus(
                    $order->id,
                    'cancelled',
                    null,
                    'Automatically cancelled after 3 days without payment',
                    null // System change
                );
                Log::info('Auto-cancelled order', ['order_id' => $order->id]);
            } catch (\Exception $e) {
                Log::error('Failed to auto-cancel order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
