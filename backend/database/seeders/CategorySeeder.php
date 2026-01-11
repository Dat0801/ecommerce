<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => [
                'Laptops',
                'Smartphones',
                'Tablets',
                'Audio',
                'Cameras'
            ],
            'Fashion' => [
                'Men Clothing',
                'Women Clothing',
                'Footwear',
                'Watches',
                'Bags'
            ],
            'Home & Living' => [
                'Furniture',
                'Decor',
                'Lighting',
                'Kitchenware'
            ],
            'Sports & Outdoors' => [
                'Gym Equipment',
                'Cycling',
                'Hiking',
                'Team Sports'
            ],
            'Books' => [
                'Fiction',
                'Non-fiction',
                'Science',
                'Business'
            ]
        ];

        foreach ($categories as $parentName => $children) {
            $parent = Category::firstOrCreate(
                ['name' => $parentName],
                ['slug' => Str::slug($parentName)]
            );

            foreach ($children as $childName) {
                Category::firstOrCreate(
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id
                    ],
                    ['slug' => Str::slug($childName)]
                );
            }
        }
    }
}
