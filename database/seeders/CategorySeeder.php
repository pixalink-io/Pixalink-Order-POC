<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Burgers', 'description' => 'Juicy burgers with fresh ingredients'],
            ['name' => 'Pizza', 'description' => 'Wood-fired authentic pizzas'],
            ['name' => 'Drinks', 'description' => 'Refreshing beverages'],
            ['name' => 'Desserts', 'description' => 'Sweet treats and desserts'],
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}
