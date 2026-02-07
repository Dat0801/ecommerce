<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Delete a product image
     */
    public function destroy($id)
    {
        $image = ProductImage::findOrFail($id);
        $productId = $image->product_id;

        // Delete file from storage
        if (Storage::disk('public')->exists(str_replace('storage/', '', $image->image_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $image->image_path));
        }

        $image->delete();

        // If this was the primary image, set another as primary
        $product = \App\Models\Product::find($productId);
        if ($product && !$product->images()->where('is_primary', true)->exists()) {
            $firstImage = $product->images()->first();
            if ($firstImage) {
                $firstImage->update(['is_primary' => true]);
                $product->update(['image' => $firstImage->image_path]);
            } else {
                $product->update(['image' => null]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
        ]);
    }

    /**
     * Set an image as primary
     */
    public function setPrimary($id)
    {
        $image = ProductImage::findOrFail($id);
        $product = $image->product;

        // Unset other primary images
        ProductImage::where('product_id', $product->id)
            ->where('id', '!=', $image->id)
            ->update(['is_primary' => false]);

        // Set this as primary
        $image->update(['is_primary' => true]);
        $product->update(['image' => $image->image_path]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated',
            'data' => $image->fresh(),
        ]);
    }

    /**
     * Reorder images
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:product_images,id',
            'images.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['images'] as $imageData) {
            ProductImage::where('id', $imageData['id'])
                ->update(['sort_order' => $imageData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully',
        ]);
    }
}
