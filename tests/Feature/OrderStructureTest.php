<?php

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('orders can be created with unique order numbers', function () {
    $order1 = Order::factory()->create();
    $order2 = Order::factory()->create();

    expect($order1->order_number)->not()->toBe($order2->order_number)
        ->and($order1->order_number)->toStartWith('ORD-');
});

test('orders have many order items', function () {
    $order = Order::factory()->create();
    OrderItem::factory()->count(3)->create(['order_id' => $order->id]);

    expect($order->orderItems)->toHaveCount(3);
});

test('order items belong to orders', function () {
    $order = Order::factory()->create();
    $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);

    expect($orderItem->order)->toBeInstanceOf(Order::class)
        ->and($orderItem->order->id)->toBe($order->id);
});

test('order items belong to menu items', function () {
    $menuItem = MenuItem::factory()->create();
    $orderItem = OrderItem::factory()->create(['menu_item_id' => $menuItem->id]);

    expect($orderItem->menuItem)->toBeInstanceOf(MenuItem::class)
        ->and($orderItem->menuItem->id)->toBe($menuItem->id);
});

test('order can transition through status workflow', function () {
    $order = Order::factory()->pending()->create();
    
    expect($order->status)->toBe('pending');

    $order->confirm();
    expect($order->fresh()->status)->toBe('confirmed')
        ->and($order->fresh()->confirmed_at)->not()->toBeNull();

    $order->startPreparing();
    expect($order->fresh()->status)->toBe('preparing')
        ->and($order->fresh()->prepared_at)->not()->toBeNull();

    $order->markReady();
    expect($order->fresh()->status)->toBe('ready')
        ->and($order->fresh()->ready_at)->not()->toBeNull();

    $order->complete();
    expect($order->fresh()->status)->toBe('completed')
        ->and($order->fresh()->completed_at)->not()->toBeNull();
});

test('order can be cancelled', function () {
    $order = Order::factory()->pending()->create();
    
    $order->cancel();
    
    expect($order->fresh()->status)->toBe('cancelled')
        ->and($order->fresh()->cancelled_at)->not()->toBeNull();
});

test('order items store selected options as json', function () {
    $orderItem = OrderItem::factory()->create([
        'selected_options' => [
            [
                'group_name' => 'Size',
                'options' => [['name' => 'Large', 'price' => 2.00]]
            ]
        ]
    ]);

    expect($orderItem->selected_options)->toBeArray()
        ->and($orderItem->selected_options[0]['group_name'])->toBe('Size');
});

test('order items calculate total correctly', function () {
    $orderItem = OrderItem::factory()->create([
        'menu_item_price' => 10.00,
        'options_total' => 2.00,
        'quantity' => 3,
    ]);

    $expectedTotal = (10.00 + 2.00) * 3;
    
    expect($orderItem->calculateTotal())->toBe($expectedTotal);
});

test('active orders scope excludes completed and cancelled', function () {
    Order::factory()->pending()->create();
    Order::factory()->preparing()->create();
    Order::factory()->completed()->create();
    Order::factory()->cancelled()->create();

    $activeOrders = Order::active()->get();

    expect($activeOrders)->toHaveCount(2);
});

test('seeded order data exists', function () {
    $this->artisan('db:seed');

    expect(Order::count())->toBeGreaterThan(0)
        ->and(OrderItem::count())->toBeGreaterThan(0);
});
