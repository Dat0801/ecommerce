<?php

namespace App\Interfaces;

interface OrderRepositoryInterface
{
    public function create(array $data);

    public function addItem($orderId, array $data);

    public function getById($id, $withItems = true);

    public function getUserOrders($userId, $perPage = 10);

    public function getAll($filters = [], $perPage = 10);

    public function updateStatus($orderId, $status);
}
