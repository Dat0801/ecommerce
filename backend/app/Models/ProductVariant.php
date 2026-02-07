<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'name',
        'price',
        'stock',
        'image',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'sort_order' => 'integer',
        'is_default' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(VariantAttributeValue::class, 'product_variant_values')
            ->withPivot('variant_attribute_id')
            ->withTimestamps();
    }

    /**
     * Get effective price (variant price or product base price)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * Get variant display name
     */
    public function getDisplayNameAttribute()
    {
        if ($this->name) {
            return $this->name;
        }

        $values = $this->attributeValues->map(function ($value) {
            return $value->display_value ?? $value->value;
        });

        return $values->implode(' - ');
    }
}
