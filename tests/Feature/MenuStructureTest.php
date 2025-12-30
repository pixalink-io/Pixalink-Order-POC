<?php

use App\Models\Category;
use App\Models\MenuItem;
// use Illuminate\Foundation\Testing\RefreshDatabase;

// uses(RefreshDatabase::class);

test('categories can be created', function () {
    $category = Category::factory()->create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    expect($category)->toBeInstanceOf(Category::class)
        ->and($category->name)->toBe('Test Category')
        ->and($category->slug)->toBe('test-category');
});

test('menu items belong to categories', function () {
    $category = Category::factory()->create();
    $menuItem = MenuItem::factory()->create([
        'category_id' => $category->id,
    ]);

    expect($menuItem->category)->toBeInstanceOf(Category::class)
        ->and($menuItem->category->id)->toBe($category->id);
});

test('categories have many menu items', function () {
    $category = Category::factory()->create();
    MenuItem::factory()->count(3)->create([
        'category_id' => $category->id,
    ]);

    expect($category->menuItems)->toHaveCount(3);
});

test('menu items can be marked as available or unavailable', function () {
    $available = MenuItem::factory()->create(['is_available' => true]);
    $unavailable = MenuItem::factory()->create(['is_available' => false]);

    expect($available->is_available)->toBeTrue()
        ->and($unavailable->is_available)->toBeFalse();
});

test('categories can be active or inactive', function () {
    $active = Category::factory()->create(['is_active' => true]);
    $inactive = Category::factory()->create(['is_active' => false]);

    expect($active->is_active)->toBeTrue()
        ->and($inactive->is_active)->toBeFalse();
});

test('seeded data exists', function () {
    $this->seed();

    expect(Category::count())->toBeGreaterThanOrEqual(4)
        ->and(MenuItem::count())->toBeGreaterThanOrEqual(7);
});
