<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_products()
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_product_as_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $data = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'TEST-SKU',
            'price' => 100.00,
            'stock' => 10,
            'status' => 'active',
        ];

        $response = $this->actingAs($admin)
            ->postJson('/api/v1/admin/products', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['sku' => 'TEST-SKU']);
    }

    public function test_cannot_create_product_as_customer()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $category = Category::factory()->create();

        $data = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'TEST-SKU',
            'price' => 100.00,
            'stock' => 10,
            'status' => 'active',
        ];

        $response = $this->actingAs($customer)
            ->postJson('/api/v1/admin/products', $data);

        $response->assertStatus(403);
    }
}
