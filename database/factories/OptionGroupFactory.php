<?php

namespace Database\Factories;
use App\Models\MenuItem;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OptionGroup>
 */
class OptionGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_item_id' => MenuItem::factory(),
            'name' => fake()->randomElement(['Size', 'Extras', 'Toppings', 'Cheese', 'Sauce']),
            'type' => fake()->randomElement(['single', 'multiple']),
            'is_required' => fake()->boolean(30),
            'min_selections' => 0,
            'max_selections' => fake()->numberBetween(1, 5),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    public function single(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'single',
            'min_selections' => 0,
            'max_selections' => 1,
        ]);
    }

    public function multiple(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'multiple',
        ]);
    }

    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
            'min_selections' => 1,
        ]);
    }
}
