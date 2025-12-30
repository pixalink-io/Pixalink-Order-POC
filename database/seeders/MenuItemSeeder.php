<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $burgers = Category::where('slug', 'burgers')->first();
        $pizza = Category::where('slug', 'pizza')->first();
        $drinks = Category::where('slug', 'drinks')->first();

        $items = [
            // Burgers
            ['category_id' => $burgers->id, 'name' => 'Classic Beef Burger', 'price' => 12.99, 'description' => 'Angus beef patty with lettuce, tomato, and special sauce'],
            ['category_id' => $burgers->id, 'name' => 'Chicken Burger', 'price' => 10.99, 'description' => 'Grilled chicken breast with mayo and pickles'],
            ['category_id' => $burgers->id, 'name' => 'Veggie Burger', 'price' => 9.99, 'description' => 'Plant-based patty with fresh vegetables'],
            
            // Pizza
            ['category_id' => $pizza->id, 'name' => 'Margherita', 'price' => 14.99, 'description' => 'Fresh mozzarella, tomato sauce, and basil'],
            ['category_id' => $pizza->id, 'name' => 'Pepperoni', 'price' => 16.99, 'description' => 'Classic pepperoni with mozzarella'],
            
            // Drinks
            ['category_id' => $drinks->id, 'name' => 'Coca Cola', 'price' => 2.99, 'description' => 'Chilled soft drink'],
            ['category_id' => $drinks->id, 'name' => 'Fresh Orange Juice', 'price' => 4.99, 'description' => 'Freshly squeezed orange juice'],
        ];

        foreach ($items as $index => $item) {
            MenuItem::create([
                'category_id' => $item['category_id'],
                'name' => $item['name'],
                'slug' => Str::slug($item['name']),
                'description' => $item['description'],
                'price' => $item['price'],
                'sort_order' => $index,
                'is_available' => true,
                'is_featured' => $index < 3, // First 3 items are featured
            ]);
        }
    }
}
