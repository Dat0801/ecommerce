<?php

namespace App\Services;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

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

            $order = $this->orders->create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'total_items' => $cart->total_items,
                'total' => $cart->total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cod',
                'payment_status' => 'unpaid',
                'shipping_name' => $data['shipping_name'],
                'shipping_email' => $data['shipping_email'] ?? null,
                'shipping_phone' => $data['shipping_phone'] ?? null,
                'shipping_address' => $data['shipping_address'],
                'note' => $data['note'] ?? null,
            ]);

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

            // Clear cart after checkout
            $this->cartRepository->clearCart($cart->id);

            return $this->formatOrderResponse(
                $this->orders->getById($order->id)
            );
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

    public function updateStatus($orderId, $status)
    {
        $order = $this->orders->updateStatus($orderId, $status);
        return $this->formatOrderResponse($order->fresh(['items.product']));
    }

    protected function formatOrderResponse($order)
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'total_items' => $order->total_items,
            'total' => $order->total,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
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
