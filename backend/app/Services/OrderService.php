<?php

namespace App\Services;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\OrderStatusHistory;
use App\Models\OrderNote;

class OrderService
{
    protected $orders;
    protected $cartRepository;

    public function __construct(
        OrderRepositoryInterface $orders,
        CartRepositoryInterface $cartRepository
    ) {
        $this->orders = $orders;
        $this->cartRepository = $cartRepository;
    }

    public function checkout(array $data, $userId = null, $sessionId = null)
    {
        return DB::transaction(function () use ($data, $userId, $sessionId) {
            $cart = $userId
                ? $this->cartRepository->getUserCart($userId)
                : $this->cartRepository->getGuestCart($sessionId);

            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception('Your cart is empty.');
            }

            // Validate stock and product availability
            foreach ($cart->items as $item) {
                if (!$item->product) {
                    throw new \Exception('One of the products is no longer available.');
                }

                if ($item->product->stock < $item->quantity) {
                    throw new \Exception('Insufficient stock for ' . $item->product->name);
                }
            }

            $paymentMethod = $data['payment_method'] ?? 'cod';
            $paymentStatus = ($paymentMethod === 'cod') ? 'pending' : 'unpaid';

            // Calculate subtotal
            $subtotal = $cart->total;
            $discountAmount = 0;
            $couponCode = null;

            // Apply coupon if provided
            if (isset($data['coupon_code']) && !empty($data['coupon_code'])) {
                $coupon = \App\Models\Coupon::where('code', $data['coupon_code'])->first();
                
                if ($coupon && $coupon->isValid() && $coupon->canBeUsedBy($userId)) {
                    if ($subtotal >= $coupon->minimum_amount) {
                        $discountAmount = $coupon->calculateDiscount($subtotal);
                        $couponCode = $coupon->code;
                        
                        // Record coupon usage
                        \App\Models\CouponUsage::create([
                            'coupon_id' => $coupon->id,
                            'user_id' => $userId,
                            'order_id' => null, // Will be updated after order creation
                            'discount_amount' => $discountAmount,
                        ]);
                        
                        // Increment coupon usage count
                        $coupon->incrementUsage();
                    }
                }
            }

            // Calculate shipping cost
            $shippingCost = 0;
            $shippingMethodId = null;
            
            if (isset($data['shipping_method_id'])) {
                $shippingService = app(\App\Services\ShippingService::class);
                try {
                    $shippingCost = $shippingService->calculateShippingCost(
                        $data['shipping_method_id'],
                        $subtotal,
                        $cart->total_items,
                        0 // totalWeight - can be added later if products have weight
                    );
                    $shippingMethodId = $data['shipping_method_id'];
                } catch (\Exception $e) {
                    // If shipping method invalid, use default (free shipping)
                    \Illuminate\Support\Facades\Log::warning('Invalid shipping method', [
                        'method_id' => $data['shipping_method_id'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $total = $subtotal - $discountAmount + $shippingCost;

            $order = $this->orders->create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'total_items' => $cart->total_items,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'shipping_method_id' => $shippingMethodId,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'] ?? null,
                'shipping_phone' => $data['shipping_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'note' => $data['note'] ?? null,
            ]);

            // Update coupon usage with order_id
            if ($couponCode) {
                \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                    ->where('user_id', $userId)
                    ->whereNull('order_id')
                    ->latest()
                    ->first()
                    ->update(['order_id' => $order->id]);
            }

            foreach ($cart->items as $item) {
                $this->orders->addItem($order->id, [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->quantity * $item->price,
                ]);

                // Reduce stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Process payment if payment data is provided (for online payments)
            if (isset($data['payment_data']) && $paymentMethod !== 'cod') {
                $paymentService = app(\App\Services\PaymentService::class);
                try {
                    $paymentResult = $paymentService->processPayment($order, $data['payment_data']);
                    $order->refresh();
                } catch (\Exception $e) {
                    // If payment fails, order remains unpaid
                    // In production, you might want to handle this differently
                    throw new \Exception('Payment processing failed: ' . $e->getMessage());
                }
            }

            // Clear cart after checkout
            $this->cartRepository->clearCart($cart->id);

            $orderResponse = $this->formatOrderResponse(
                $this->orders->getById($order->id)
            );

            // Send order confirmation email
            if ($order->shipping_email) {
                try {
                    \Illuminate\Support\Facades\Notification::route('mail', $order->shipping_email)
                        ->notify(new \App\Notifications\OrderConfirmation($order));
                } catch (\Exception $e) {
                    // Log error but don't fail the checkout
                    \Illuminate\Support\Facades\Log::error('Failed to send order confirmation email', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $orderResponse;
        });
    }

    public function getUserOrders($userId, $perPage = 10)
    {
        $orders = $this->orders->getUserOrders($userId, $perPage);
        $orders->getCollection()->transform(function ($order) {
            return $this->formatOrderResponse($order);
        });
        return $orders;
    }

    public function getOrderById($orderId, $userId = null, $isAdmin = false)
    {
        $order = $this->orders->getById($orderId);

        if (!$isAdmin && $order->user_id !== $userId) {
            throw new \Exception('Unauthorized access to this order');
        }

        return $this->formatOrderResponse($order);
    }

    public function getAdminOrders($filters = [], $perPage = 10)
    {
        $orders = $this->orders->getAll($filters, $perPage);
        $orders->getCollection()->transform(function ($order) {
            return $this->formatOrderResponse($order);
        });
        return $orders;
    }

    public function updateStatus($orderId, $status, $trackingData = null, $note = null, $changedBy = null)
    {
        return DB::transaction(function () use ($orderId, $status, $trackingData, $note, $changedBy) {
            $order = $this->orders->getById($orderId);
            $oldStatus = $order->status;

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $status)) {
                throw new \Exception("Invalid status transition from '{$oldStatus}' to '{$status}'");
            }

            $updateData = ['status' => $status];
            
            // If status is 'shipped', add tracking information
            if ($status === 'shipped' && $trackingData) {
                $updateData['tracking_number'] = $trackingData['tracking_number'] ?? null;
                $updateData['tracking_url'] = $trackingData['tracking_url'] ?? null;
                $updateData['shipped_at'] = now();
            }
            
            $order = $this->orders->updateStatus($orderId, $status, $updateData);

            // Record status history
            \App\Models\OrderStatusHistory::create([
                'order_id' => $orderId,
                'status_from' => $oldStatus,
                'status_to' => $status,
                'changed_by' => $changedBy ?? auth('sanctum')->id(),
                'note' => $note,
            ]);

            $order = $order->fresh(['items.product']);

            // Send email notifications
            if ($order->shipping_email && $oldStatus !== $status) {
                try {
                    // Send status update notification
                    \Illuminate\Support\Facades\Notification::route('mail', $order->shipping_email)
                        ->notify(new \App\Notifications\OrderStatusUpdate($order, $oldStatus, $status));

                    // Send special notification for shipped orders
                    if ($status === 'shipped') {
                        \Illuminate\Support\Facades\Notification::route('mail', $order->shipping_email)
                            ->notify(new \App\Notifications\OrderShipped($order));
                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the status update
                    \Illuminate\Support\Facades\Log::error('Failed to send order status update email', [
                        'order_id' => $orderId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $this->formatOrderResponse($order);
        });
    }

    /**
     * Validate status transition
     */
    protected function isValidStatusTransition($from, $to)
    {
        $validTransitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['completed'],
            'completed' => [], // No transitions from completed
            'cancelled' => [], // No transitions from cancelled
        ];

        return in_array($to, $validTransitions[$from] ?? []);
    }

    /**
     * Get valid next statuses for an order
     */
    public function getValidNextStatuses($orderId)
    {
        $order = $this->orders->getById($orderId);
        $currentStatus = $order->status;

        $validTransitions = [
            'pending' => ['paid', 'cancelled'],
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        return $validTransitions[$currentStatus] ?? [];
    }

    /**
     * Add note to order
     */
    public function addNote($orderId, $note, $isInternal = true, $userId = null)
    {
        return \App\Models\OrderNote::create([
            'order_id' => $orderId,
            'user_id' => $userId ?? auth('sanctum')->id(),
            'note' => $note,
            'is_internal' => $isInternal,
        ]);
    }

    /**
     * Get order status history
     */
    public function getStatusHistory($orderId)
    {
        return \App\Models\OrderStatusHistory::where('order_id', $orderId)
            ->with('changedBy')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get order notes
     */
    public function getNotes($orderId, $includeInternal = false)
    {
        $query = \App\Models\OrderNote::where('order_id', $orderId)
            ->with('user');

        if (!$includeInternal) {
            $query->where('is_internal', false);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function updateTracking($orderId, $trackingNumber, $trackingUrl = null)
    {
        $order = $this->orders->getById($orderId);
        
        if (!$order) {
            throw new \Exception('Order not found');
        }

        $order->update([
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl,
            'status' => $order->status === 'pending' ? 'shipped' : $order->status,
            'shipped_at' => $order->shipped_at ?? now(),
        ]);

        return $this->formatOrderResponse($order->fresh(['items.product']));
    }

    public function getOrderByTrackingNumber($trackingNumber)
    {
        $order = $this->orders->getByTrackingNumber($trackingNumber);
        
        if (!$order) {
            throw new \Exception('Order not found');
        }

        return $this->formatOrderResponse($order);
    }

    public function cancelOrder($orderId, $userId, $reason = null)
    {
        $order = $this->orders->getById($orderId);

        // Check authorization
        if (!$order->user_id || $order->user_id !== $userId) {
            throw new \Exception('Unauthorized access to this order');
        }

        // Check if order can be cancelled
        $cancellableStatuses = ['pending', 'paid', 'processing'];
        if (!in_array($order->status, $cancellableStatuses)) {
            throw new \Exception('Order cannot be cancelled. Current status: ' . $order->status);
        }

        // Check if order has been shipped
        if ($order->tracking_number) {
            throw new \Exception('Order has already been shipped and cannot be cancelled.');
        }

        return DB::transaction(function () use ($order, $reason) {
            // Restore product stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
                'note' => $order->note ? $order->note . "\n\nCancelled: " . ($reason ?? 'No reason provided') : 'Cancelled: ' . ($reason ?? 'No reason provided'),
            ]);

            // Refund payment if paid
            if ($order->payment_status === 'paid') {
                $order->update(['payment_status' => 'refunded']);
                
                // In production, you would integrate with payment gateway for refund
                // For now, we just mark it as refunded
            }

            return $this->formatOrderResponse($order->fresh(['items.product']));
        });
    }

    protected function formatOrderResponse($order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'total_items' => $order->total_items,
            'subtotal' => $order->subtotal ?? $order->total,
            'discount_amount' => $order->discount_amount ?? 0,
            'coupon_code' => $order->coupon_code,
            'shipping_method' => $order->shippingMethod ? [
                'id' => $order->shippingMethod->id,
                'name' => $order->shippingMethod->name,
                'code' => $order->shippingMethod->code,
            ] : null,
            'shipping_cost' => $order->shipping_cost ?? 0,
            'total' => $order->total,
            'status' => $order->status,
            'tracking_number' => $order->tracking_number,
            'tracking_url' => $order->tracking_url,
            'shipped_at' => $order->shipped_at,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_transaction_id' => $order->payment_transaction_id,
            'shipping_name' => $order->shipping_name,
            'shipping_email' => $order->shipping_email,
            'shipping_phone' => $order->shipping_phone,
            'shipping_address' => $order->shipping_address,
            'note' => $order->note,
            'created_at' => $order->created_at,
            'items' => $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'product_image' => $item->product?->image,
                ];
            }),
        ];
    }
}
