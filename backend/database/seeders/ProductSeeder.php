<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Define some realistic products for specific categories
        $productTemplates = [
            'Laptops' => [
                ['name' => 'MacBook Pro 16 M3', 'price' => 2499, 'desc' => 'The most powerful MacBook Pro ever.'],
                ['name' => 'Dell XPS 15', 'price' => 1899, 'desc' => 'High performance in a sleek design.'],
                ['name' => 'Lenovo ThinkPad X1', 'price' => 1599, 'desc' => 'Built for business, ready for anything.'],
                ['name' => 'Asus ROG Zephyrus', 'price' => 2199, 'desc' => 'Ultra-slim gaming laptop.']
            ],
            'Smartphones' => [
                ['name' => 'iPhone 15 Pro Max', 'price' => 1199, 'desc' => 'Titanium design, A17 Pro chip.'],
                ['name' => 'Samsung Galaxy S24 Ultra', 'price' => 1299, 'desc' => 'Galaxy AI is here.'],
                ['name' => 'Google Pixel 8 Pro', 'price' => 999, 'desc' => 'The most advanced Pixel camera yet.'],
                ['name' => 'Xiaomi 14 Ultra', 'price' => 1099, 'desc' => 'Leica optics for professional photography.']
            ],
            'Tablets' => [
                ['name' => 'iPad Pro 12.9', 'price' => 1099, 'desc' => 'The ultimate iPad experience.'],
                ['name' => 'Samsung Galaxy Tab S9', 'price' => 799, 'desc' => 'Water and dust resistant tablet.']
            ],
            'Audio' => [
                ['name' => 'Sony WH-1000XM5', 'price' => 349, 'desc' => 'Industry-leading noise cancellation.'],
                ['name' => 'AirPods Pro 2', 'price' => 249, 'desc' => 'Magic like you’ve never heard.'],
                ['name' => 'Bose QuietComfort Ultra', 'price' => 429, 'desc' => 'World-class noise cancellation.']
            ],
            'Men Clothing' => [
                ['name' => 'Classic Oxford Shirt', 'price' => 59, 'desc' => 'Timeless style for any occasion.'],
                ['name' => 'Slim Fit Chinos', 'price' => 49, 'desc' => 'Comfortable and stylish everyday pants.'],
                ['name' => 'Leather Jacket', 'price' => 299, 'desc' => 'Premium genuine leather jacket.']
            ],
            'Women Clothing' => [
                ['name' => 'Floral Summer Dress', 'price' => 79, 'desc' => 'Light and breezy for summer days.'],
                ['name' => 'High Waisted Jeans', 'price' => 69, 'desc' => 'Flattering fit denim.'],
                ['name' => 'Cashmere Sweater', 'price' => 149, 'desc' => 'Luxuriously soft warmth.']
            ],
            'Furniture' => [
                ['name' => 'Modern Sofa', 'price' => 899, 'desc' => 'Minimalist design with maximum comfort.'],
                ['name' => 'Oak Dining Table', 'price' => 599, 'desc' => 'Solid wood table for family gatherings.'],
                ['name' => 'Ergonomic Office Chair', 'price' => 299, 'desc' => 'Support for long working hours.']
            ],
            'Watches' => [
                ['name' => 'Classic Chronograph', 'price' => 199, 'desc' => 'Elegant timepiece for everyday wear.'],
                ['name' => 'Smart Fitness Watch', 'price' => 149, 'desc' => 'Track your health and workouts.']
            ]
        ];

        foreach ($productTemplates as $categoryName => $products) {
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                foreach ($products as $p) {
                    Product::create([
                        'category_id' => $category->id,
                        'name' => $p['name'],
                        'slug' => Str::slug($p['name']) . '-' . Str::random(5),
                        'sku' => strtoupper(Str::random(3)) . '-' . rand(10000, 99999),
                        'price' => $p['price'],
                        'stock' => rand(10, 100),
                        'status' => 'active',
                        'description' => $p['desc'],
                        'image' => 'https://placehold.co/600x400?text=' . urlencode($p['name'])
                    ]);
                }
            }
        }

        // Fill other categories with random products using factory
        $categoriesWithoutProducts = Category::whereDoesntHave('products')
            ->whereNotNull('parent_id') // Only leaf categories
            ->get();

        foreach ($categoriesWithoutProducts as $category) {
            Product::factory()->count(5)->create([
                'category_id' => $category->id
            ]);
        }
    }
}
