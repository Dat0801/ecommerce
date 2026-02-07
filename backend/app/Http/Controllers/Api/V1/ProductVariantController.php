<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    /**
     * Get variants for a product
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $variants = $product->variants()->with('attributeValues.attribute')->get();

        return response()->json([
            'success' => true,
            'data' => $variants,
        ]);
    }

    /**
     * Create a variant for a product (Admin)
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'sku' => 'required|string|unique:product_variants,sku',
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_default' => 'boolean',
            'attribute_values' => 'required|array',
            'attribute_values.*.attribute_id' => 'required|exists:variant_attributes,id',
            'attribute_values.*.value_id' => 'required|exists:variant_attribute_values,id',
        ]);

        return DB::transaction(function () use ($product, $validated, $productId) {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'variants/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public', $imageName);
                $imagePath = 'storage/' . $imageName;
            }

            // If this is default, unset other defaults
            if ($validated['is_default'] ?? false) {
                ProductVariant::where('product_id', $productId)
                    ->update(['is_default' => false]);
            }

            $variant = ProductVariant::create([
                'product_id' => $productId,
                'sku' => $validated['sku'],
                'name' => $validated['name'] ?? null,
                'price' => $validated['price'] ?? null,
                'stock' => $validated['stock'],
                'image' => $imagePath,
                'is_default' => $validated['is_default'] ?? false,
            ]);

            // Attach attribute values
            foreach ($validated['attribute_values'] as $attrValue) {
                $variant->attributeValues()->attach($attrValue['value_id'], [
                    'variant_attribute_id' => $attrValue['attribute_id'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product variant created successfully',
                'data' => $variant->load('attributeValues.attribute'),
            ], 201);
        });
    }

    /**
     * Update a variant (Admin)
     */
    public function update(Request $request, $productId, $variantId)
    {
        $variant = ProductVariant::where('product_id', $productId)
            ->findOrFail($variantId);

        $validated = $request->validate([
            'sku' => 'sometimes|string|unique:product_variants,sku,' . $variantId,
            'name' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_default' => 'boolean',
            'attribute_values' => 'sometimes|array',
            'attribute_values.*.attribute_id' => 'required|exists:variant_attributes,id',
            'attribute_values.*.value_id' => 'required|exists:variant_attribute_values,id',
        ]);

        return DB::transaction(function () use ($variant, $validated, $productId) {
            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($variant->image && \Illuminate\Support\Facades\Storage::disk('public')->exists(str_replace('storage/', '', $variant->image))) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $variant->image));
                }

                $image = $request->file('image');
                $imageName = 'variants/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public', $imageName);
                $validated['image'] = 'storage/' . $imageName;
            }

            // If this is default, unset other defaults
            if (isset($validated['is_default']) && $validated['is_default']) {
                ProductVariant::where('product_id', $productId)
                    ->where('id', '!=', $variant->id)
                    ->update(['is_default' => false]);
            }

            $variant->update($validated);

            // Update attribute values if provided
            if (isset($validated['attribute_values'])) {
                $variant->attributeValues()->detach();
                foreach ($validated['attribute_values'] as $attrValue) {
                    $variant->attributeValues()->attach($attrValue['value_id'], [
                        'variant_attribute_id' => $attrValue['attribute_id'],
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Product variant updated successfully',
                'data' => $variant->fresh()->load('attributeValues.attribute'),
            ]);
        });
    }

    /**
     * Delete a variant (Admin)
     */
    public function destroy($productId, $variantId)
    {
        $variant = ProductVariant::where('product_id', $productId)
            ->findOrFail($variantId);

        // Delete image if exists
        if ($variant->image && \Illuminate\Support\Facades\Storage::disk('public')->exists(str_replace('storage/', '', $variant->image))) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $variant->image));
        }

        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product variant deleted successfully',
        ]);
    }
}
