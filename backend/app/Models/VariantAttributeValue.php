<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_attribute_id',
        'value',
        'display_value',
        'color_code',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function attribute()
    {
        return $this->belongsTo(VariantAttribute::class, 'variant_attribute_id');
    }

    public function productVariants()
    {
        return $this->belongsToMany(ProductVariant::class, 'product_variant_values')
            ->withPivot('variant_attribute_id')
            ->withTimestamps();
    }
}
