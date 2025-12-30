<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Option;
use App\Models\OptionGroup;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $optionGroups = OptionGroup::with('menuItem')->get();

        foreach ($optionGroups as $group) {
            if ($group->name === 'Size') {
                // Size options based on item type
                if (str_contains($group->menuItem->name, 'Burger')) {
                    Option::create(['option_group_id' => $group->id, 'name' => 'Regular', 'price_adjustment' => 0, 'sort_order' => 0]);
                    Option::create(['option_group_id' => $group->id, 'name' => 'Large', 'price_adjustment' => 2.00, 'sort_order' => 1]);
                } elseif (str_contains($group->menuItem->name, 'Pizza') || str_contains($group->menuItem->category->name, 'Pizza')) {
                    Option::create(['option_group_id' => $group->id, 'name' => 'Small (10")', 'price_adjustment' => 0, 'sort_order' => 0]);
                    Option::create(['option_group_id' => $group->id, 'name' => 'Medium (12")', 'price_adjustment' => 3.00, 'sort_order' => 1]);
                    Option::create(['option_group_id' => $group->id, 'name' => 'Large (14")', 'price_adjustment' => 5.00, 'sort_order' => 2]);
                } elseif (str_contains($group->menuItem->category->name, 'Drink')) {
                    Option::create(['option_group_id' => $group->id, 'name' => 'Small', 'price_adjustment' => 0, 'sort_order' => 0]);
                    Option::create(['option_group_id' => $group->id, 'name' => 'Medium', 'price_adjustment' => 0.50, 'sort_order' => 1]);
                    Option::create(['option_group_id' => $group->id, 'name' => 'Large', 'price_adjustment' => 1.00, 'sort_order' => 2]);
                }
            } elseif ($group->name === 'Extras') {
                Option::create(['option_group_id' => $group->id, 'name' => 'Extra Cheese', 'price_adjustment' => 1.50, 'sort_order' => 0]);
                Option::create(['option_group_id' => $group->id, 'name' => 'Bacon', 'price_adjustment' => 2.00, 'sort_order' => 1]);
                Option::create(['option_group_id' => $group->id, 'name' => 'Avocado', 'price_adjustment' => 1.50, 'sort_order' => 2]);
            } elseif ($group->name === 'Extra Toppings') {
                Option::create(['option_group_id' => $group->id, 'name' => 'Mushrooms', 'price_adjustment' => 1.00, 'sort_order' => 0]);
                Option::create(['option_group_id' => $group->id, 'name' => 'Olives', 'price_adjustment' => 1.00, 'sort_order' => 1]);
                Option::create(['option_group_id' => $group->id, 'name' => 'Bell Peppers', 'price_adjustment' => 1.00, 'sort_order' => 2]);
                Option::create(['option_group_id' => $group->id, 'name' => 'Extra Cheese', 'price_adjustment' => 1.50, 'sort_order' => 3]);
            }
        }
    }
}
