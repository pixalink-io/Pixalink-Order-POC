<?php

namespace Database\Seeders;


use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = MenuItem::with('optionGroups.options')->get();

        // Create 5 sample orders with different statuses
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed'];

        foreach ($statuses as $index => $status) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => fake()->name(),
                'customer_phone' => fake()->phoneNumber(),
                'customer_email' => fake()->email(),
                'notes' => fake()->optional()->sentence(),
                'status' => $status,
                'subtotal' => 0,
                'tax' => 0,
                'total' => 0,
            ]);

            // Add 2-4 items to each order
            $itemCount = rand(2, 4);
            $subtotal = 0;

            for ($i = 0; $i < $itemCount; $i++) {
                $menuItem = $menuItems->random();
                $quantity = rand(1, 3);

                // Simulate selected options
                $selectedOptions = [];
                $optionsTotal = 0;

                foreach ($menuItem->optionGroups as $group) {
                    $option = $group->options->random();
                    $selectedOptions[] = [
                        'group_name' => $group->name,
                        'group_type' => $group->type,
                        'options' => [
                            [
                                'name' => $option->name,
                                'price' => (float) $option->price_adjustment,
                            ]
                        ]
                    ];
                    $optionsTotal += (float) $option->price_adjustment;
                }

                $itemTotal = ((float) $menuItem->price + $optionsTotal) * $quantity;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'menu_item_name' => $menuItem->name,
                    'menu_item_price' => $menuItem->price,
                    'quantity' => $quantity,
                    'selected_options' => $selectedOptions,
                    'options_total' => $optionsTotal,
                    'item_total' => $itemTotal,
                    'special_instructions' => fake()->optional()->sentence(),
                ]);
            }

            // Update order totals
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            // Set timestamps based on status
            if ($status === 'confirmed') {
                $order->update(['confirmed_at' => now()->subMinutes(5)]);
            } elseif ($status === 'preparing') {
                $order->update([
                    'confirmed_at' => now()->subMinutes(10),
                    'prepared_at' => now()->subMinutes(5),
                ]);
            } elseif ($status === 'ready') {
                $order->update([
                    'confirmed_at' => now()->subMinutes(15),
                    'prepared_at' => now()->subMinutes(10),
                    'ready_at' => now()->subMinutes(5),
                ]);
            } elseif ($status === 'completed') {
                $order->update([
                    'confirmed_at' => now()->subMinutes(20),
                    'prepared_at' => now()->subMinutes(15),
                    'ready_at' => now()->subMinutes(10),
                    'completed_at' => now()->subMinutes(5),
                ]);
            }
        }
    }
}
