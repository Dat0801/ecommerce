<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\DB;

class ReturnService
{
    /**
     * Create a return request
     */
    public function createReturnRequest($orderId, $userId, array $data)
    {
        return DB::transaction(function () use ($orderId, $userId, $data) {
            $order = Order::where('id', $orderId)
                ->where('user_id', $userId)
                ->with('items')
                ->firstOrFail();

            // Check if order can be returned
            if (!in_array($order->status, ['completed', 'shipped'])) {
                throw new \Exception('Order cannot be returned. Only completed or shipped orders can be returned.');
            }

            // Check if return already exists
            $existingReturn = OrderReturn::where('order_id', $orderId)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingReturn) {
                throw new \Exception('A return request already exists for this order.');
            }

            // Validate return items
            $totalRefundAmount = 0;
            foreach ($data['items'] as $returnItem) {
                $orderItem = $order->items->find($returnItem['order_item_id']);
                if (!$orderItem) {
                    throw new \Exception('Invalid order item.');
                }
                if ($returnItem['quantity'] > $orderItem->quantity) {
                    throw new \Exception('Return quantity cannot exceed ordered quantity.');
                }
                $totalRefundAmount += ($orderItem->product_price * $returnItem['quantity']);
            }

            // Create return request
            $return = OrderReturn::create([
                'order_id' => $orderId,
                'user_id' => $userId,
                'status' => 'pending',
                'reason' => $data['reason'],
                'description' => $data['description'] ?? null,
                'refund_amount' => $totalRefundAmount,
                'refund_method' => $data['refund_method'] ?? 'original',
                'refund_status' => 'pending',
            ]);

            // Create return items
            foreach ($data['items'] as $returnItem) {
                \App\Models\OrderReturnItem::create([
                    'order_return_id' => $return->id,
                    'order_item_id' => $returnItem['order_item_id'],
                    'quantity' => $returnItem['quantity'],
                    'reason' => $returnItem['reason'] ?? null,
                ]);
            }

            return $return->load(['items.orderItem.product', 'order']);
        });
    }

    /**
     * Approve a return request (Admin)
     */
    public function approveReturn($returnId, $adminNotes = null)
    {
        return DB::transaction(function () use ($returnId, $adminNotes) {
            $return = OrderReturn::with(['items.orderItem', 'order'])->findOrFail($returnId);

            if (!$return->canBeApproved()) {
                throw new \Exception('Return cannot be approved. Current status: ' . $return->status);
            }

            $return->update([
                'status' => 'approved',
                'approved_at' => now(),
                'admin_notes' => $adminNotes,
            ]);

            // Restore stock for returned items
            foreach ($return->items as $returnItem) {
                $orderItem = $returnItem->orderItem;
                if ($orderItem->product) {
                    $orderItem->product->increment('stock', $returnItem->quantity);
                }
            }

            return $return->fresh();
        });
    }

    /**
     * Process refund (Admin)
     */
    public function processRefund($returnId)
    {
        return DB::transaction(function () use ($returnId) {
            $return = OrderReturn::with('order')->findOrFail($returnId);

            if ($return->status !== 'approved') {
                throw new \Exception('Return must be approved before processing refund.');
            }

            if ($return->refund_status === 'completed') {
                throw new \Exception('Refund already processed.');
            }

            // Update refund status
            $return->update([
                'refund_status' => 'processing',
            ]);

            // In production, integrate with payment gateway for actual refund
            // For now, we'll mark it as completed
            $return->update([
                'refund_status' => 'completed',
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Update order payment status if fully refunded
            $order = $return->order;
            $totalReturned = OrderReturn::where('order_id', $order->id)
                ->where('refund_status', 'completed')
                ->sum('refund_amount');

            if ($totalReturned >= $order->total) {
                $order->update(['payment_status' => 'refunded']);
            }

            return $return->fresh();
        });
    }

    /**
     * Reject a return request (Admin)
     */
    public function rejectReturn($returnId, $adminNotes)
    {
        $return = OrderReturn::findOrFail($returnId);

        if ($return->status !== 'pending') {
            throw new \Exception('Only pending returns can be rejected.');
        }

        $return->update([
            'status' => 'rejected',
            'admin_notes' => $adminNotes,
        ]);

        return $return->fresh();
    }

    /**
     * Cancel a return request (Customer)
     */
    public function cancelReturn($returnId, $userId)
    {
        $return = OrderReturn::where('id', $returnId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if (!$return->canBeCancelled()) {
            throw new \Exception('Return cannot be cancelled. Current status: ' . $return->status);
        }

        $return->update([
            'status' => 'cancelled',
        ]);

        return $return->fresh();
    }
}
