<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderItem;

class OrderRepository implements OrderRepositoryInterface
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function addItem($orderId, array $data)
    {
        return OrderItem::create(array_merge($data, [
            'order_id' => $orderId,
        ]));
    }

    public function getById($id, $withItems = true)
    {
        $query = Order::query();

        if ($withItems) {
            $query->with(['items.product']);
        }

        return $query->findOrFail($id);
    }

    public function getUserOrders($userId, $perPage = 10)
    {
        return Order::where('user_id', $userId)
            ->with(['items'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getAll($filters = [], $perPage = 10)
    {
        $query = Order::query()->with(['user', 'items']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->status = $status;
        $order->save();

        return $order;
    }
}
