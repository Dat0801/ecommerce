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
            'payment_method' => 'nullable|string|in:cod,stripe,paypal',
            'payment_data' => 'nullable|array',
            'coupon_code' => 'nullable|string|max:50',
            'shipping_method_id' => 'nullable|exists:shipping_methods,id',
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
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            $trackingData = null;
            if ($validated['status'] === 'shipped' && isset($validated['tracking_number'])) {
                $trackingData = [
                    'tracking_number' => $validated['tracking_number'],
                    'tracking_url' => $validated['tracking_url'] ?? null,
                ];
            }

            $order = $this->orderService->updateStatus(
                $orderId,
                $validated['status'],
                $trackingData,
                $validated['note'] ?? null,
                auth('sanctum')->id()
            );

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

    /**
     * Get valid next statuses for an order (Admin)
     */
    public function getValidStatuses($orderId)
    {
        try {
            $validStatuses = $this->orderService->getValidNextStatuses($orderId);

            return response()->json([
                'success' => true,
                'data' => $validStatuses,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get order status history (Admin)
     */
    public function getStatusHistory($orderId)
    {
        try {
            $history = $this->orderService->getStatusHistory($orderId);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get order status history (Customer)
     */
    public function getStatusHistoryCustomer($orderId)
    {
        try {
            $userId = auth('sanctum')->id();
            $order = \App\Models\Order::where('id', $orderId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $history = $this->orderService->getStatusHistory($orderId);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Add note to order (Admin)
     */
    public function addNote(Request $request, $orderId)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
            'is_internal' => 'boolean',
        ]);

        try {
            $note = $this->orderService->addNote(
                $orderId,
                $validated['note'],
                $validated['is_internal'] ?? true,
                auth('sanctum')->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully',
                'data' => $note->load('user'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get order notes (Admin)
     */
    public function getNotes($orderId)
    {
        try {
            $notes = $this->orderService->getNotes($orderId, true); // Include internal notes

            return response()->json([
                'success' => true,
                'data' => $notes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get order notes (Customer - only public notes)
     */
    public function getNotesCustomer($orderId)
    {
        try {
            $userId = auth('sanctum')->id();
            $order = \App\Models\Order::where('id', $orderId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $notes = $this->orderService->getNotes($orderId, false); // Only public notes

            return response()->json([
                'success' => true,
                'data' => $notes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Update order note (Admin)
     */
    public function updateNote(Request $request, $orderId, $noteId)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
        ]);

        try {
            $note = \App\Models\OrderNote::where('order_id', $orderId)
                ->findOrFail($noteId);

            $note->update(['note' => $validated['note']]);

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully',
                'data' => $note->fresh()->load('user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Delete order note (Admin)
     */
    public function deleteNote($orderId, $noteId)
    {
        try {
            $note = \App\Models\OrderNote::where('order_id', $orderId)
                ->findOrFail($noteId);

            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function trackOrder(Request $request, $trackingNumber)
    {
        try {
            $order = $this->orderService->getOrderByTrackingNumber($trackingNumber);

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

    public function cancel(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $userId = auth('sanctum')->id();
            $order = $this->orderService->cancelOrder($id, $userId, $validated['reason'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
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
