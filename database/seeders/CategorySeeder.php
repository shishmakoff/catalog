<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Смартфоны'],
            ['name' => 'Ноутбуки'],
            ['name' => 'Телевизоры'],
            ['name' => 'Наушники'],
            ['name' => 'Фотоаппараты'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
