<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of shipping methods (Admin).
     */
    public function index(Request $request)
    {
        $methods = ShippingMethod::orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }

    /**
     * Store a newly created shipping method.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:shipping_methods,code|max:50',
            'type' => 'required|in:fixed,weight_based,item_based,price_based',
            'base_cost' => 'required|numeric|min:0',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days_min' => 'nullable|integer|min:1',
            'estimated_days_max' => 'nullable|integer|min:1|gte:estimated_days_min',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $method = ShippingMethod::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method created successfully',
            'data' => $method,
        ], 201);
    }

    /**
     * Update the specified shipping method.
     */
    public function update(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:shipping_methods,code,' . $id . '|max:50',
            'type' => 'sometimes|in:fixed,weight_based,item_based,price_based',
            'base_cost' => 'sometimes|numeric|min:0',
            'cost_per_kg' => 'nullable|numeric|min:0',
            'cost_per_item' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'estimated_days_min' => 'nullable|integer|min:1',
            'estimated_days_max' => 'nullable|integer|min:1|gte:estimated_days_min',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $method->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method updated successfully',
            'data' => $method->fresh(),
        ]);
    }

    /**
     * Remove the specified shipping method.
     */
    public function destroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping method deleted successfully',
        ]);
    }
}
