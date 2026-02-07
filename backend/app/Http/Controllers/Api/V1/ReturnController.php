<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    protected $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Display a listing of return requests.
     */
    public function index(Request $request)
    {
        $userId = auth('sanctum')->id();
        $isAdmin = auth('sanctum')->user()->role === 'admin';

        $query = \App\Models\OrderReturn::with(['order', 'items.orderItem.product']);

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by order_id
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $returns = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $returns,
        ]);
    }

    /**
     * Store a newly created return request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|in:damaged,wrong_item,not_as_described,defective,other',
            'description' => 'nullable|string|max:1000',
            'refund_method' => 'nullable|in:original,store_credit',
            'items' => 'required|array|min:1',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:500',
        ]);

        try {
            $userId = auth('sanctum')->id();
            $return = $this->returnService->createReturnRequest(
                $validated['order_id'],
                $userId,
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'Return request submitted successfully',
                'data' => $return,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified return request.
     */
    public function show($id)
    {
        $userId = auth('sanctum')->id();
        $isAdmin = auth('sanctum')->user()->role === 'admin';

        $query = \App\Models\OrderReturn::with(['order.items.product', 'items.orderItem.product', 'user']);

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        $return = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $return,
        ]);
    }

    /**
     * Cancel a return request (Customer).
     */
    public function cancel($id)
    {
        try {
            $userId = auth('sanctum')->id();
            $return = $this->returnService->cancelReturn($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Return request cancelled successfully',
                'data' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Approve a return request (Admin).
     */
    public function approve(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $return = $this->returnService->approveReturn($id, $validated['admin_notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Return request approved',
                'data' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reject a return request (Admin).
     */
    public function reject(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        try {
            $return = $this->returnService->rejectReturn($id, $validated['admin_notes']);

            return response()->json([
                'success' => true,
                'message' => 'Return request rejected',
                'data' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Process refund (Admin).
     */
    public function processRefund($id)
    {
        try {
            $return = $this->returnService->processRefund($id);

            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully',
                'data' => $return,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
