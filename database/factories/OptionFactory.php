<?php

namespace Database\Factories;

use App\Models\OptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option>
 */
class OptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'option_group_id' => OptionGroup::factory(),
            'name' => fake()->randomElement([
                'Small', 'Medium', 'Large',
                'Extra Cheese', 'Extra Sauce',
                'Pepperoni', 'Mushrooms', 'Olives',
                'BBQ Sauce', 'Ranch', 'Ketchup'
            ]),
            'price_adjustment' => fake()->randomFloat(2, 0, 5),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_adjustment' => 0,
        ]);
    }
}
