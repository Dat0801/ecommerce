<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get user's cart
     */
    public function index(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $sessionId = $this->getOrCreateSessionId($request);

            $cart = $this->cartService->getCart($userId, $sessionId);

            return response()->json([
                'success' => true,
                'data' => $cart ?? ['items' => [], 'total_items' => 0, 'total' => 0],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $userId = auth('sanctum')->id();
            $sessionId = $this->getOrCreateSessionId($request);

            $cart = $this->cartService->addToCart(
                $request->product_id,
                $request->quantity,
                $userId,
                $sessionId
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        try {
            $userId = auth('sanctum')->id();
            $sessionId = $this->getOrCreateSessionId($request);

            $cart = $this->cartService->updateQuantity(
                $itemId,
                $request->quantity,
                $userId,
                $sessionId
            );

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $itemId)
    {
        try {
            $userId = auth('sanctum')->id();
            $sessionId = $this->getOrCreateSessionId($request);

            $cart = $this->cartService->removeItem(
                $itemId,
                $userId,
                $sessionId
            );

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $sessionId = $this->getOrCreateSessionId($request);

            $result = $this->cartService->clearCart($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Merge guest cart to user cart after login
     */
    public function merge(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        try {
            $userId = auth('sanctum')->id();

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User must be authenticated',
                ], 401);
            }

            $cart = $this->cartService->mergeGuestCart(
                $request->session_id,
                $userId
            );

            return response()->json([
                'success' => true,
                'message' => 'Carts merged successfully',
                'data' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get or create session ID for guest users
     */
    protected function getOrCreateSessionId(Request $request)
    {
        $sessionId = $request->header('X-Session-Id') ?? $request->cookie('cart_session');

        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            cookie()->queue('cart_session', $sessionId, 60 * 24 * 30); // 30 days
        }

        return $sessionId;
    }
}
