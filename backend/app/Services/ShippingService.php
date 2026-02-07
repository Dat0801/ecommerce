<?php

namespace App\Services;

use App\Models\ShippingMethod;

class ShippingService
{
    /**
     * Get all active shipping methods
     */
    public function getActiveMethods()
    {
        return ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Calculate shipping cost for a given method and order details
     */
    public function calculateShippingCost($shippingMethodId, $subtotal, $itemCount = 0, $totalWeight = 0)
    {
        $shippingMethod = ShippingMethod::find($shippingMethodId);

        if (!$shippingMethod || !$shippingMethod->is_active) {
            throw new \Exception('Invalid or inactive shipping method.');
        }

        return $shippingMethod->calculateCost($subtotal, $totalWeight, $itemCount);
    }

    /**
     * Get shipping methods with calculated costs
     */
    public function getMethodsWithCosts($subtotal, $itemCount = 0, $totalWeight = 0)
    {
        $methods = $this->getActiveMethods();

        return $methods->map(function ($method) use ($subtotal, $itemCount, $totalWeight) {
            $cost = $method->calculateCost($subtotal, $totalWeight, $itemCount);
            $estimatedDelivery = $method->getEstimatedDeliveryRange();

            return [
                'id' => $method->id,
                'name' => $method->name,
                'code' => $method->code,
                'type' => $method->type,
                'cost' => $cost,
                'estimated_delivery' => $estimatedDelivery,
                'description' => $method->description,
            ];
        });
    }
}
