<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    public function getUserCart($userId)
    {
        return Cart::with(['items.product.category'])
            ->where('user_id', $userId)
            ->first();
    }

    public function getGuestCart($sessionId)
    {
        return Cart::with(['items.product.category'])
            ->where('session_id', $sessionId)
            ->whereNull('user_id')
            ->first();
    }

    public function createCart(array $data)
    {
        return Cart::create($data);
    }

    public function addItem($cartId, array $data)
    {
        $existingItem = CartItem::where('cart_id', $cartId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $data['quantity'];
            $existingItem->save();
            return $existingItem;
        }

        return CartItem::create([
            'cart_id' => $cartId,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    public function updateItemQuantity($cartItemId, $quantity)
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->quantity = $quantity;
        $item->save();
        return $item;
    }

    public function removeItem($cartItemId)
    {
        $item = CartItem::findOrFail($cartItemId);
        $item->delete();
        return true;
    }

    public function clearCart($cartId)
    {
        CartItem::where('cart_id', $cartId)->delete();
        return true;
    }

    public function getCartWithItems($cartId)
    {
        return Cart::with(['items.product.category'])->findOrFail($cartId);
    }

    public function mergeGuestCartToUser($sessionId, $userId)
    {
        $guestCart = $this->getGuestCart($sessionId);
        
        if (!$guestCart) {
            return null;
        }

        $userCart = $this->getUserCart($userId);
        
        if (!$userCart) {
            // Convert guest cart to user cart
            $guestCart->user_id = $userId;
            $guestCart->session_id = null;
            $guestCart->save();
            return $guestCart;
        }

        // Merge items from guest cart to user cart
        foreach ($guestCart->items as $guestItem) {
            $this->addItem($userCart->id, [
                'product_id' => $guestItem->product_id,
                'quantity' => $guestItem->quantity,
                'price' => $guestItem->price,
            ]);
        }

        // Delete guest cart
        $guestCart->delete();

        return $userCart->fresh(['items.product.category']);
    }
}
