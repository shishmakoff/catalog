<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 14 Pro',
                'price' => 99999.99,
                'category_id' => 1,
                'in_stock' => true,
                'rating' => 4.8,
            ],
            [
                'name' => 'Samsung Galaxy S23',
                'price' => 79999.99,
                'category_id' => 1,
                'in_stock' => true,
                'rating' => 4.7,
            ],
            [
                'name' => 'Xiaomi Redmi Note 12',
                'price' => 19999.99,
                'category_id' => 1,
                'in_stock' => false,
                'rating' => 4.2,
            ],

            [
                'name' => 'MacBook Pro 16"',
                'price' => 249999.99,
                'category_id' => 2,
                'in_stock' => true,
                'rating' => 4.9,
            ],
            [
                'name' => 'ASUS ROG Strix G15',
                'price' => 149999.99,
                'category_id' => 2,
                'in_stock' => true,
                'rating' => 4.5,
            ],
            [
                'name' => 'Lenovo IdeaPad 3',
                'price' => 39999.99,
                'category_id' => 2,
                'in_stock' => true,
                'rating' => 4.0,
            ],

            [
                'name' => 'Samsung QLED 4K 55"',
                'price' => 89999.99,
                'category_id' => 3,
                'in_stock' => true,
                'rating' => 4.6,
            ],
            [
                'name' => 'LG OLED 65"',
                'price' => 129999.99,
                'category_id' => 3,
                'in_stock' => false,
                'rating' => 4.8,
            ],

            [
                'name' => 'Apple AirPods Pro',
                'price' => 24999.99,
                'category_id' => 4,
                'in_stock' => true,
                'rating' => 4.7,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'price' => 34999.99,
                'category_id' => 4,
                'in_stock' => true,
                'rating' => 4.9,
            ],

            [
                'name' => 'Canon EOS R6',
                'price' => 179999.99,
                'category_id' => 5,
                'in_stock' => true,
                'rating' => 4.8,
            ],
            [
                'name' => 'Sony Alpha 7 III',
                'price' => 159999.99,
                'category_id' => 5,
                'in_stock' => true,
                'rating' => 4.7,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
