<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $shippingService;

    public function __construct(ShippingService $shippingService)
    {
        $this->shippingService = $shippingService;
    }

    /**
     * Get available shipping methods with calculated costs
     */
    public function getMethods(Request $request)
    {
        $validated = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'item_count' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0', // in grams
        ]);

        $methods = $this->shippingService->getMethodsWithCosts(
            $validated['subtotal'],
            $validated['item_count'] ?? 0,
            $validated['weight'] ?? 0
        );

        return response()->json([
            'success' => true,
            'data' => $methods,
        ]);
    }

    /**
     * Calculate shipping cost for a specific method
     */
    public function calculateCost(Request $request)
    {
        $validated = $request->validate([
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'subtotal' => 'required|numeric|min:0',
            'item_count' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
        ]);

        try {
            $cost = $this->shippingService->calculateShippingCost(
                $validated['shipping_method_id'],
                $validated['subtotal'],
                $validated['item_count'] ?? 0,
                $validated['weight'] ?? 0
            );

            return response()->json([
                'success' => true,
                'cost' => $cost,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
