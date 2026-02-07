<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist.
     */
    public function index(Request $request)
    {
        $userId = auth('sanctum')->id();
        
        $wishlist = Wishlist::where('user_id', $userId)
            ->with(['product.category'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $wishlist,
        ]);
    }

    /**
     * Add a product to wishlist.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = auth('sanctum')->id();
        $productId = $validated['product_id'];

        // Check if product already in wishlist
        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist.',
            ], 400);
        }

        $wishlist = Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'data' => $wishlist->load('product.category'),
        ], 201);
    }

    /**
     * Remove a product from wishlist.
     */
    public function destroy($productId)
    {
        $userId = auth('sanctum')->id();
        
        $wishlist = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->firstOrFail();

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist',
        ]);
    }

    /**
     * Check if a product is in wishlist.
     */
    public function check($productId)
    {
        $userId = auth('sanctum')->id();
        
        $exists = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'in_wishlist' => $exists,
        ]);
    }

    /**
     * Get wishlist count.
     */
    public function count()
    {
        $userId = auth('sanctum')->id();
        
        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
