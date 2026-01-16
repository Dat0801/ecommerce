<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'nullable|email|max:255',
            'shipping_phone' => 'nullable|string|max:50',
            'shipping_address' => 'required|string',
            'note' => 'nullable|string',
            'payment_method' => 'nullable|string',
        ]);

        try {
            $userId = auth('sanctum')->id();
            $sessionId = $request->header('X-Session-Id');

            $order = $this->orderService->checkout($validated, $userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Checkout failed', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function index(Request $request)
    {
        try {
            $userId = auth('sanctum')->id();
            $orders = $this->orderService->getUserOrders($userId, $request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $userId = auth('sanctum')->id();
            $order = $this->orderService->getOrderById($id, $userId);

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function adminIndex(Request $request)
    {
        $filters = [
            'status' => $request->get('status'),
        ];

        try {
            $orders = $this->orderService->getAdminOrders($filters, $request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function adminUpdateStatus(Request $request, $orderId)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        try {
            $order = $this->orderService->updateStatus($orderId, $validated['status']);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
