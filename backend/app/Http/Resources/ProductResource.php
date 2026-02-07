<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'price' => $this->price,
            'stock' => $this->stock,
            'status' => $this->status,
            'description' => $this->description,
            'image' => $this->image,
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'image_path' => $img->image_path,
                        'is_primary' => $img->is_primary,
                        'sort_order' => $img->sort_order,
                    ];
                });
            }),
            'has_variants' => $this->hasVariants(),
            'variants' => $this->whenLoaded('variants', function () {
                return $this->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'name' => $variant->display_name,
                        'price' => $variant->effective_price,
                        'stock' => $variant->stock,
                        'image' => $variant->image,
                        'is_default' => $variant->is_default,
                        'attributes' => $variant->whenLoaded('attributeValues', function () use ($variant) {
                            return $variant->attributeValues->map(function ($value) {
                                return [
                                    'attribute' => $value->attribute->name,
                                    'value' => $value->value,
                                    'display_value' => $value->display_value,
                                    'color_code' => $value->color_code,
                                ];
                            });
                        }),
                    ];
                });
            }),
            'min_price' => $this->min_variant_price,
            'max_price' => $this->max_variant_price,
            'average_rating' => round($this->average_rating, 2),
            'total_reviews' => $this->total_reviews,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
