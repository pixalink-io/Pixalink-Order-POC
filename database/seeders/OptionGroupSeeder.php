<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MenuItem;
use App\Models\OptionGroup;
use Illuminate\Database\Seeder;

class OptionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get menu items
        $classicBurger = MenuItem::where('slug', 'classic-beef-burger')->first();
        $chickenBurger = MenuItem::where('slug', 'chicken-burger')->first();
        $margherita = MenuItem::where('slug', 'margherita')->first();
        $pepperoni = MenuItem::where('slug', 'pepperoni')->first();
        $cocaCola = MenuItem::where('slug', 'coca-cola')->first();

        // Burger Size Options
        if ($classicBurger) {
            OptionGroup::create([
                'menu_item_id' => $classicBurger->id,
                'name' => 'Size',
                'type' => 'single',
                'is_required' => true,
                'min_selections' => 1,
                'max_selections' => 1,
                'sort_order' => 0,
            ]);

            OptionGroup::create([
                'menu_item_id' => $classicBurger->id,
                'name' => 'Extras',
                'type' => 'multiple',
                'is_required' => false,
                'min_selections' => 0,
                'max_selections' => null,
                'sort_order' => 1,
            ]);
        }

        if ($chickenBurger) {
            OptionGroup::create([
                'menu_item_id' => $chickenBurger->id,
                'name' => 'Size',
                'type' => 'single',
                'is_required' => true,
                'min_selections' => 1,
                'max_selections' => 1,
                'sort_order' => 0,
            ]);
        }

        // Pizza Size Options
        if ($margherita) {
            OptionGroup::create([
                'menu_item_id' => $margherita->id,
                'name' => 'Size',
                'type' => 'single',
                'is_required' => true,
                'min_selections' => 1,
                'max_selections' => 1,
                'sort_order' => 0,
            ]);

            OptionGroup::create([
                'menu_item_id' => $margherita->id,
                'name' => 'Extra Toppings',
                'type' => 'multiple',
                'is_required' => false,
                'min_selections' => 0,
                'max_selections' => 5,
                'sort_order' => 1,
            ]);
        }

        if ($pepperoni) {
            OptionGroup::create([
                'menu_item_id' => $pepperoni->id,
                'name' => 'Size',
                'type' => 'single',
                'is_required' => true,
                'min_selections' => 1,
                'max_selections' => 1,
                'sort_order' => 0,
            ]);
        }

        // Drink Size
        if ($cocaCola) {
            OptionGroup::create([
                'menu_item_id' => $cocaCola->id,
                'name' => 'Size',
                'type' => 'single',
                'is_required' => true,
                'min_selections' => 1,
                'max_selections' => 1,
                'sort_order' => 0,
            ]);
        }
    }
}
