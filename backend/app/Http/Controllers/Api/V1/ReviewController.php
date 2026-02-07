<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for a product.
     */
    public function index(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $reviews = Review::where('product_id', $productId)
            ->approved()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $reviews,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'average_rating' => round($product->average_rating, 2),
                'total_reviews' => $product->total_reviews,
            ],
        ]);
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $userId = auth('sanctum')->id();
        $product = Product::findOrFail($productId);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product.',
            ], 400);
        }

        // Check if order_id belongs to user and contains this product
        $isVerifiedPurchase = false;
        if (isset($validated['order_id'])) {
            $order = \App\Models\Order::where('id', $validated['order_id'])
                ->where('user_id', $userId)
                ->with('items')
                ->first();

            if ($order) {
                $isVerifiedPurchase = $order->items->contains('product_id', $productId);
            }
        }

        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'order_id' => $validated['order_id'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => false, // Requires admin approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully. It will be published after approval.',
            'data' => $review->load('user:id,name'),
        ], 201);
    }

    /**
     * Display the specified review.
     */
    public function show($id)
    {
        $review = Review::with(['user:id,name', 'product:id,name'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    /**
     * Update the specified review.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:2000',
        ]);

        $userId = auth('sanctum')->id();
        $review = Review::where('user_id', $userId)->findOrFail($id);

        // Only allow update if not approved yet
        if ($review->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update an approved review.',
            ], 400);
        }

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review->fresh()->load('user:id,name'),
        ]);
    }

    /**
     * Remove the specified review.
     */
    public function destroy($id)
    {
        $userId = auth('sanctum')->id();
        $review = Review::where('user_id', $userId)->findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }

    /**
     * Admin: Approve a review
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully',
            'data' => $review->fresh(),
        ]);
    }

    /**
     * Admin: Reject a review
     */
    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Review rejected',
            'data' => $review->fresh(),
        ]);
    }
}
