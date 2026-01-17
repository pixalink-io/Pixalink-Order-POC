<?php

use App\Models\Order;

test('kitchen can view pending orders', function () {
    $pendingOrder = Order::factory()->pending()->create();

    $response = $this->get('/admin/kitchen-display');

    $response->assertStatus(200);
    $response->assertSee($pendingOrder->order_number);
});

test('kitchen can update order status from pending to preparing', function () {
    $order = Order::factory()->create([
        'status' => 'pending',
        'confirmed_at' => now(),
    ]);

    Livewire::test(App\Livewire\KitchenDisplay::class)
        ->call('updateOrderStatus', $order->id, 'preparing')
        ->assertDispatched('orderUpdated');

    expect($order->fresh()->status)->toBe('preparing');
    expect($order->fresh()->prepared_at)->not->toBeNull();
});

test('kitchen can update order status from preparing to ready', function () {
    $order = Order::factory()->create([
        'status' => 'preparing',
        'confirmed_at' => now(),
        'prepared_at' => now(),
    ]);

    Livewire::test(App\Livewire\KitchenDisplay::class)
        ->call('updateOrderStatus', $order->id, 'ready');

    expect($order->fresh()->status)->toBe('ready');
    expect($order->fresh()->ready_at)->not->toBeNull();
});

test('kitchen can complete an order', function () {
    $order = Order::factory()->create([
        'status' => 'ready',
        'confirmed_at' => now(),
        'prepared_at' => now(),
        'ready_at' => now(),
    ]);

    Livewire::test(App\Livewire\KitchenDisplay::class)
        ->call('updateOrderStatus', $order->id, 'completed');

    expect($order->fresh()->status)->toBe('completed');
    expect($order->fresh()->completed_at)->not->toBeNull();
});

test('completed orders are not shown in kitchen display', function () {
    $completedOrder = Order::factory()->completed()->create();
    $pendingOrder = Order::factory()->pending()->create();

    $component = Livewire::test(App\Livewire\KitchenDisplay::class);

    $component->assertDontSee($completedOrder->order_number);
    $component->assertSee($pendingOrder->order_number);
});

test('old pending orders show alert', function () {
    $oldOrder = Order::factory()->create([
        'status' => 'pending',
        'created_at' => now()->subMinutes(20), // Over 15 minutes
    ]);

    $component = Livewire::test(App\Livewire\KitchenDisplay::class);

    $component->assertSee('URGENT');
});

test('kitchen can filter orders by status', function () {
    $pendingOrder = Order::factory()->pending()->create();
    $preparingOrder = Order::factory()->create(['status' => 'preparing', 'prepared_at' => now()]);

    $component = Livewire::test(App\Livewire\KitchenDisplay::class)
        ->call('setFilter', 'pending');

    $component->assertSee($pendingOrder->order_number);
    $component->assertDontSee($preparingOrder->order_number);
});

test('kitchen can view order details in modal', function () {
    $order = Order::factory()->pending()->create();

    $component = Livewire::test(App\Livewire\KitchenDisplay::class)
        ->call('showOrderDetails', $order->id);

    $component->assertSet('showDetailModal', true);
    $component->assertSet('selectedOrder.id', $order->id);
});
