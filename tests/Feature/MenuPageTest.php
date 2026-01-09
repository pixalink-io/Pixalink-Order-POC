<?php

use App\Models\Category;
use App\Models\MenuItem;

test('menu page can be rendered', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('menu page displays active menu items', function () {
    $category = Category::factory()->create(['is_active' => true]);
    $menuItem = MenuItem::factory()->create([
        'category_id' => $category->id,
        'is_available' => true,
        'name' => 'Test Burger'
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Test Burger');
    $response->assertSee($category->name);
});

test('menu page does not display inactive items', function () {
    $category = Category::factory()->create(['is_active' => true]);
    MenuItem::factory()->create([
        'category_id' => $category->id,
        'is_available' => false,
        'name' => 'Inactive Item'
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('Inactive Item');
});

test('menu page can filter by category', function () {
    $category1 = Category::factory()->create(['is_active' => true, 'name' => 'Burgers']);
    $category2 = Category::factory()->create(['is_active' => true, 'name' => 'Pizza']);

    $burger = MenuItem::factory()->create([
        'category_id' => $category1->id,
        'is_available' => true,
        'name' => 'Cheeseburger'
    ]);

    $pizza = MenuItem::factory()->create([
        'category_id' => $category2->id,
        'is_available' => true,
        'name' => 'Margherita'
    ]);

    $response = $this->get('/?category=' . $category1->id);

    $response->assertStatus(200);
    $response->assertSee('Cheeseburger');
    $response->assertDontSee('Margherita');
});

test('menu page displays all categories in filter', function () {
    $category1 = Category::factory()->create(['is_active' => true, 'name' => 'Appetizers']);
    $category2 = Category::factory()->create(['is_active' => true, 'name' => 'Main Course']);
    $category3 = Category::factory()->create(['is_active' => false, 'name' => 'Inactive Category']);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Appetizers');
    $response->assertSee('Main Course');
    $response->assertDontSee('Inactive Category');
});

test('menu page displays price correctly', function () {
    $category = Category::factory()->create(['is_active' => true]);
    MenuItem::factory()->create([
        'category_id' => $category->id,
        'is_available' => true,
        'price' => 12.50
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('12.50');
});

test('menu page shows empty state when no items', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('No items available');
});
