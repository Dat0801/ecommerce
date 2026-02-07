<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'stock',
        'status',
        'description',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('is_primary', 'desc');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    /**
     * Check if product has variants
     */
    public function hasVariants()
    {
        return $this->variants()->count() > 0;
    }

    /**
     * Get minimum variant price
     */
    public function getMinVariantPriceAttribute()
    {
        if (!$this->hasVariants()) {
            return $this->price;
        }

        return $this->variants()->min('price') ?? $this->price;
    }

    /**
     * Get maximum variant price
     */
    public function getMaxVariantPriceAttribute()
    {
        if (!$this->hasVariants()) {
            return $this->price;
        }

        return $this->variants()->max('price') ?? $this->price;
    }
}
