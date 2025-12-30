<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $menuItem = MenuItem::inRandomOrder()->first() ?? MenuItem::factory()->create();
        $quantity = fake()->numberBetween(1, 3);
        $optionsTotal = fake()->randomFloat(2, 0, 5);
        $itemTotal = ($menuItem->price + $optionsTotal) * $quantity;

        return [
            'order_id' => Order::factory(),
            'menu_item_id' => $menuItem->id,
            'menu_item_name' => $menuItem->name,
            'menu_item_price' => $menuItem->price,
            'quantity' => $quantity,
            'selected_options' => [
                [
                    'group_name' => 'Size',
                    'group_type' => 'single',
                    'options' => [
                        ['name' => 'Large', 'price' => 2.00]
                    ]
                ]
            ],
            'options_total' => $optionsTotal,
            'item_total' => $itemTotal,
            'special_instructions' => fake()->optional()->sentence(),
        ];
    }

    public function withoutOptions(): static
    {
        return $this->state(fn (array $attributes) => [
            'selected_options' => null,
            'options_total' => 0,
            'item_total' => $attributes['menu_item_price'] * $attributes['quantity'],
        ]);
    }
}
