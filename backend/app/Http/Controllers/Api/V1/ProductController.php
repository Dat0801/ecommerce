<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
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
        $filters = $request->all();
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
            'image' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);

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
            'image' => 'nullable|string',
        ]);

        $data = $request->all();
        if (isset($data['name'])) {
             $data['slug'] = Str::slug($data['name']) . '-' . Str::random(5);
        }

        $product = $this->productService->updateProduct($id, $data);

        return new ProductResource($product);
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
