<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'base_cost',
        'cost_per_kg',
        'cost_per_item',
        'free_shipping_threshold',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
        'sort_order',
        'description',
    ];

    protected $casts = [
        'base_cost' => 'decimal:2',
        'cost_per_kg' => 'decimal:2',
        'cost_per_item' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'estimated_days_min' => 'integer',
        'estimated_days_max' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Calculate shipping cost based on order details
     */
    public function calculateCost($subtotal, $totalWeight = 0, $itemCount = 0)
    {
        // Check if free shipping threshold is met
        if ($this->free_shipping_threshold && $subtotal >= $this->free_shipping_threshold) {
            return 0;
        }

        $cost = $this->base_cost;

        switch ($this->type) {
            case 'weight_based':
                if ($this->cost_per_kg && $totalWeight > 0) {
                    $cost += ($totalWeight / 1000) * $this->cost_per_kg; // Convert grams to kg
                }
                break;

            case 'item_based':
                if ($this->cost_per_item && $itemCount > 0) {
                    $cost += $itemCount * $this->cost_per_item;
                }
                break;

            case 'price_based':
                // Can be implemented based on subtotal percentage
                break;

            case 'fixed':
            default:
                // Just base cost
                break;
        }

        return max(0, round($cost, 2));
    }

    /**
     * Get estimated delivery date range
     */
    public function getEstimatedDeliveryRange()
    {
        if (!$this->estimated_days_min || !$this->estimated_days_max) {
            return null;
        }

        $minDate = now()->addDays($this->estimated_days_min);
        $maxDate = now()->addDays($this->estimated_days_max);

        return [
            'min' => $minDate->format('Y-m-d'),
            'max' => $maxDate->format('Y-m-d'),
        ];
    }
}
