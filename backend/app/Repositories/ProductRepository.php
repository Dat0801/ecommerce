<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll()
    {
        $perPage = request('per_page', 12);
        return Product::with(['category', 'images'])->paginate($perPage);
    }

    public function getById($id)
    {
        return Product::with(['category', 'images'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        return Product::destroy($id);
    }

    public function getFiltered($filters = [])
    {
        $query = Product::query()->with(['category', 'images']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = trim($filters['search']);
            $searchTerms = explode(' ', $search);
            
            $query->where(function($q) use ($search, $searchTerms) {
                // Exact match on name (highest priority)
                $q->where('name', 'like', "%{$search}%");
                
                // Match individual words in name
                foreach ($searchTerms as $term) {
                    if (strlen($term) > 2) {
                        $q->orWhere('name', 'like', "%{$term}%");
                    }
                }
                
                // Search in SKU
                $q->orWhere('sku', 'like', "%{$search}%");
                
                // Search in description
                $q->orWhere('description', 'like', "%{$search}%");
                
                // Search in category name through relationship
                $q->orWhereHas('category', function($categoryQuery) use ($search) {
                    $categoryQuery->where('name', 'like', "%{$search}%");
                });
            });
            
            // Add relevance ordering for search results
            // Products with exact name match come first, then SKU match, then description
            $query->orderByRaw("
                CASE 
                    WHEN name LIKE ? THEN 1
                    WHEN sku LIKE ? THEN 2
                    WHEN description LIKE ? THEN 3
                    ELSE 4
                END ASC
            ", ["%{$search}%", "%{$search}%", "%{$search}%"]);
        } else {
            // Default ordering when no search
            $query->orderBy('created_at', 'desc');
        }
        
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['in_stock'])) {
            if ($filters['in_stock']) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '<=', 0);
            }
        }

        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    if (!isset($filters['search'])) {
                        $query->orderBy('created_at', 'desc');
                    }
            }
        } elseif (!isset($filters['search'])) {
            // Default ordering when no search and no sort specified
            $query->orderBy('created_at', 'desc');
        }

        $perPage = $filters['per_page'] ?? 12;
        return $query->paginate($perPage);
    }
}
