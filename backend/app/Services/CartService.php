<?php

namespace App\Services;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartService
{
    protected $cartRepository;
    protected $productRepository;

    public function __construct(
        CartRepositoryInterface $cartRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    public function getCart($userId = null, $sessionId = null)
    {
        if ($userId) {
            $cart = $this->cartRepository->getUserCart($userId);
        } elseif ($sessionId) {
            $cart = $this->cartRepository->getGuestCart($sessionId);
        } else {
            return null;
        }

        if (!$cart) {
            return null;
        }

        return $this->formatCartResponse($cart);
    }

    public function addToCart($productId, $quantity, $userId = null, $sessionId = null)
    {
        return DB::transaction(function () use ($productId, $quantity, $userId, $sessionId) {
            // Get product
            $product = $this->productRepository->find($productId);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            if ($product->stock < $quantity) {
                throw new \Exception('Insufficient stock');
            }

            // Get or create cart
            $cart = $userId 
                ? $this->cartRepository->getUserCart($userId)
                : $this->cartRepository->getGuestCart($sessionId);

            if (!$cart) {
                $cart = $this->cartRepository->createCart([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                ]);
            }

            // Add item to cart
            $this->cartRepository->addItem($cart->id, [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);

            // Reload cart with items
            $cart = $this->cartRepository->getCartWithItems($cart->id);

            return $this->formatCartResponse($cart);
        });
    }

    public function updateQuantity($cartItemId, $quantity, $userId = null, $sessionId = null)
    {
        return DB::transaction(function () use ($cartItemId, $quantity, $userId, $sessionId) {
            if ($quantity <= 0) {
                return $this->removeItem($cartItemId, $userId, $sessionId);
            }

            $item = $this->cartRepository->updateItemQuantity($cartItemId, $quantity);

            // Check stock
            if ($item->product->stock < $quantity) {
                throw new \Exception('Insufficient stock');
            }

            $cart = $this->cartRepository->getCartWithItems($item->cart_id);

            return $this->formatCartResponse($cart);
        });
    }

    public function removeItem($cartItemId, $userId = null, $sessionId = null)
    {
        return DB::transaction(function () use ($cartItemId, $userId, $sessionId) {
            $this->cartRepository->removeItem($cartItemId);

            // Get updated cart
            $cart = $userId 
                ? $this->cartRepository->getUserCart($userId)
                : $this->cartRepository->getGuestCart($sessionId);

            if (!$cart) {
                return null;
            }

            return $this->formatCartResponse($cart);
        });
    }

    public function clearCart($userId = null, $sessionId = null)
    {
        $cart = $userId 
            ? $this->cartRepository->getUserCart($userId)
            : $this->cartRepository->getGuestCart($sessionId);

        if ($cart) {
            $this->cartRepository->clearCart($cart->id);
        }

        return ['message' => 'Cart cleared successfully'];
    }

    public function mergeGuestCart($sessionId, $userId)
    {
        $cart = $this->cartRepository->mergeGuestCartToUser($sessionId, $userId);

        if (!$cart) {
            return null;
        }

        return $this->formatCartResponse($cart);
    }

    protected function formatCartResponse($cart)
    {
        return [
            'id' => $cart->id,
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                        'price' => $item->product->price,
                        'stock' => $item->product->stock,
                        'image' => $item->product->image,
                        'category' => $item->product->category ? [
                            'id' => $item->product->category->id,
                            'name' => $item->product->category->name,
                        ] : null,
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal,
                ];
            }),
            'total_items' => $cart->total_items,
            'total' => $cart->total,
        ];
    }
}
