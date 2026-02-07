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
            'average_rating' => round($this->average_rating, 2),
            'total_reviews' => $this->total_reviews,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
