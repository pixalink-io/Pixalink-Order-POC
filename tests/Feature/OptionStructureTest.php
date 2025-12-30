<?php

use App\Models\MenuItem;
use App\Models\Option;
use App\Models\OptionGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('option groups belong to menu items', function () {
    $menuItem = MenuItem::factory()->create();
    $optionGroup = OptionGroup::factory()->create([
        'menu_item_id' => $menuItem->id,
    ]);

    expect($optionGroup->menuItem)->toBeInstanceOf(MenuItem::class)
        ->and($optionGroup->menuItem->id)->toBe($menuItem->id);
});

test('menu items have many option groups', function () {
    $menuItem = MenuItem::factory()->create();
    OptionGroup::factory()->count(3)->create([
        'menu_item_id' => $menuItem->id,
    ]);

    expect($menuItem->optionGroups)->toHaveCount(3);
});

test('options belong to option groups', function () {
    $optionGroup = OptionGroup::factory()->create();
    $option = Option::factory()->create([
        'option_group_id' => $optionGroup->id,
    ]);

    expect($option->optionGroup)->toBeInstanceOf(OptionGroup::class)
        ->and($option->optionGroup->id)->toBe($optionGroup->id);
});

test('option groups have many options', function () {
    $optionGroup = OptionGroup::factory()->create();
    Option::factory()->count(4)->create([
        'option_group_id' => $optionGroup->id,
    ]);

    expect($optionGroup->options)->toHaveCount(4);
});

test('option groups can be single or multiple type', function () {
    $single = OptionGroup::factory()->single()->create();
    $multiple = OptionGroup::factory()->multiple()->create();

    expect($single->type)->toBe('single')
        ->and($multiple->type)->toBe('multiple');
});

test('option groups can be required', function () {
    $required = OptionGroup::factory()->required()->create();
    $optional = OptionGroup::factory()->create(['is_required' => false]);

    expect($required->is_required)->toBeTrue()
        ->and($optional->is_required)->toBeFalse();
});

test('options have price adjustments', function () {
    $free = Option::factory()->free()->create();
    $paid = Option::factory()->create(['price_adjustment' => 2.50]);

    expect($free->price_adjustment)->toBe('0.00')
        ->and($paid->price_adjustment)->toBe('2.50');
});

test('options can be unavailable', function () {
    $available = Option::factory()->create();
    $unavailable = Option::factory()->unavailable()->create();

    expect($available->is_available)->toBeTrue()
        ->and($unavailable->is_available)->toBeFalse();
});

test('seeded option data exists', function () {
    $this->artisan('db:seed');

    expect(OptionGroup::count())->toBeGreaterThan(0)
        ->and(Option::count())->toBeGreaterThan(0);
});
