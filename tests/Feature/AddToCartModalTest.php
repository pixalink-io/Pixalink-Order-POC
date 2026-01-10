<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\OptionGroup;
use App\Models\Option;
use App\Livewire\AddToCartModal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class AddToCartModalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function modal_can_be_opened_with_menu_item()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->assertSet('isOpen', true)
            ->assertSet('menuItem.id', $menuItem->id);
    }

    /** @test */
    public function modal_can_be_closed()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->assertSet('isOpen', true)
            ->call('closeModal')
            ->assertSet('isOpen', false)
            ->assertSet('menuItem', null);
    }

    /** @test */
    public function quantity_can_be_incremented()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->assertSet('quantity', 1)
            ->call('incrementQuantity')
            ->assertSet('quantity', 2)
            ->call('incrementQuantity')
            ->assertSet('quantity', 3);
    }

    /** @test */
    public function quantity_can_be_decremented()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->set('quantity', 3)
            ->call('decrementQuantity')
            ->assertSet('quantity', 2)
            ->call('decrementQuantity')
            ->assertSet('quantity', 1);
    }

    /** @test */
    public function quantity_cannot_go_below_one()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->assertSet('quantity', 1)
            ->call('decrementQuantity')
            ->assertSet('quantity', 1);
    }

    /** @test */
    public function quantity_cannot_exceed_99()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->set('quantity', 99)
            ->call('incrementQuantity')
            ->assertSet('quantity', 99);
    }

    /** @test */
    public function can_select_single_option()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
            'name' => 'Size',
        ]);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Small',
        ]);

        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Large',
        ]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option1->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, $option1->id)
            ->call('selectOption', $optionGroup->id, $option2->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, $option2->id);
    }

    /** @test */
    public function can_select_multiple_options()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'multiple',
            'name' => 'Toppings',
            'max_selections' => 5,
        ]);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Cheese',
        ]);

        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Bacon',
        ]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option1->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, [$option1->id])
            ->call('selectOption', $optionGroup->id, $option2->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, [$option1->id, $option2->id]);
    }

    /** @test */
    public function can_deselect_multiple_options()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'multiple',
            'name' => 'Toppings',
        ]);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Cheese',
        ]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option1->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, [$option1->id])
            ->call('selectOption', $optionGroup->id, $option1->id)
            ->assertSet('selectedOptions', function ($selectedOptions) use ($optionGroup) {
                return !isset($selectedOptions[$optionGroup->id]) || empty($selectedOptions[$optionGroup->id]);
            });
    }

    /** @test */
    public function respects_max_selections_limit()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'multiple',
            'name' => 'Toppings',
            'max_selections' => 2,
        ]);

        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option3 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option1->id)
            ->call('selectOption', $optionGroup->id, $option2->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, [$option1->id, $option2->id])
            ->call('selectOption', $optionGroup->id, $option3->id)
            ->assertSet('errors', function ($errors) {
                return !empty($errors);
            })
            ->assertSet('selectedOptions.' . $optionGroup->id, [$option1->id, $option2->id]);
    }

    /** @test */
    public function validates_required_options_before_adding_to_cart()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
            'name' => 'Size',
            'is_required' => true,
        ]);

        // Create multiple options to prevent auto-selection
        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Small',
        ]);
        
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => 'Large',
        ]);

        $component = Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id);
        
        // Clear preselected options if any
        $component->set('selectedOptions', [])
            ->call('addToCart')
            ->assertSet('errors', function ($errors) {
                return !empty($errors);
            });
        
        // Verify the error message contains "required"
        $errors = $component->get('errors');
        $this->assertTrue(
            !empty($errors) && str_contains(strtolower(implode(' ', $errors)), 'required'),
            'Expected error to contain "required" but got: ' . implode(', ', $errors)
        );
    }

    /** @test */
    public function can_add_item_to_cart_with_options()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000,
        ]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
            'name' => 'Size',
        ]);

        $option = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price_adjustment' => 200,
        ]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option->id)
            ->set('quantity', 2)
            ->call('addToCart')
            ->assertDispatched('cartUpdated')
            ->assertSet('isOpen', false);

        // Verify item was added to session
        $this->assertNotEmpty(session('shopping_cart'));
    }

    /** @test */
    public function can_add_special_instructions()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $instructions = 'No onions please';

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->set('specialInstructions', $instructions)
            ->call('addToCart')
            ->assertDispatched('cartUpdated');

        $cart = session('shopping_cart');
        $this->assertEquals($instructions, array_values($cart)[0]['special_instructions']);
    }

    /** @test */
    public function calculates_item_total_correctly()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000,
        ]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
        ]);

        $option = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price_adjustment' => 200,
        ]);

        $component = Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->call('selectOption', $optionGroup->id, $option->id)
            ->set('quantity', 2);

        // (1000 + 200) * 2 = 2400
        $this->assertEquals(2400, $component->get('itemTotal'));
    }

    /** @test */
    public function is_option_selected_returns_correct_value()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
            'is_required' => false, // Ensure it's not required to avoid preselection
        ]);

        $option = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        $component = Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id);

        // Clear any preselected options
        $component->set('selectedOptions', []);

        $this->assertFalse($component->instance()->isOptionSelected($optionGroup->id, $option->id));

        $component->call('selectOption', $optionGroup->id, $option->id);

        $this->assertTrue($component->instance()->isOptionSelected($optionGroup->id, $option->id));
    }

    /** @test */
    public function preselects_default_options_when_only_one_required_option()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
            'is_required' => true,
        ]);

        $option = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->assertSet('selectedOptions.' . $optionGroup->id, $option->id);
    }

    /** @test */
    public function special_instructions_has_max_length_validation()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $longText = str_repeat('a', 501);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->set('specialInstructions', $longText)
            ->call('addToCart')
            ->assertHasErrors(['specialInstructions' => 'max']);
    }

    /** @test */
    public function prevents_adding_to_cart_while_already_adding()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        Livewire::test(AddToCartModal::class)
            ->dispatch('openAddToCartModal', menuItemId: $menuItem->id)
            ->set('isAdding', true)
            ->call('addToCart')
            ->assertSet('isOpen', true); // Should still be open because it didn't process
    }
}
