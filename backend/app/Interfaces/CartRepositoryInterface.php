<?php

namespace App\Interfaces;

interface CartRepositoryInterface
{
    public function getUserCart($userId);
    public function getGuestCart($sessionId);
    public function createCart(array $data);
    public function addItem($cartId, array $data);
    public function updateItemQuantity($cartItemId, $quantity);
    public function removeItem($cartItemId);
    public function clearCart($cartId);
    public function getCartWithItems($cartId);
    public function mergeGuestCartToUser($sessionId, $userId);
}
