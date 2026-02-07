<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,draft',
            'in_stock' => 'nullable|boolean',
            'sort' => 'nullable|in:price_asc,price_desc,name_asc,name_desc,newest,oldest',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);
        
        $products = $this->productService->getFilteredProducts($filters);
        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'string|in:active,inactive,draft',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'products/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public', $imageName);
            $data['image'] = 'storage/' . $imageName;
        } elseif ($request->has('image') && is_string($request->image)) {
            // Allow string URL for backward compatibility
            $data['image'] = $request->image;
        } else {
            $data['image'] = null;
        }

        $product = $this->productService->createProduct($data);

        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'sku' => 'string|unique:products,sku,' . $id,
            'price' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'status' => 'string|in:active,inactive,draft',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();
        if (isset($data['name'])) {
             $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        }

        // Get existing product to check for old image
        $product = $this->productService->getProductById($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }

            $image = $request->file('image');
            $imageName = 'products/' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('public', $imageName);
            $data['image'] = 'storage/' . $imageName;
        } elseif ($request->has('image') && is_string($request->image)) {
            // Allow string URL for backward compatibility
            $data['image'] = $request->image;
        } elseif ($request->has('image') && $request->image === null) {
            // Delete image if explicitly set to null
            if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
            }
            $data['image'] = null;
        }

        $product = $this->productService->updateProduct($id, $data);

        return new ProductResource($product);
    }

    public function destroy($id)
    {
        // Get product to delete associated image
        $product = $this->productService->getProductById($id);
        
        // Delete associated image if exists
        if ($product->image && Storage::disk('public')->exists(str_replace('storage/', '', $product->image))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $product->image));
        }
        
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
