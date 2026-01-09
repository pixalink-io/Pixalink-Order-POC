<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\OptionGroup;
use App\Models\Option;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    /** @test */
    public function can_add_item_to_cart()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1500,
        ]);

        $result = $this->cartService->addItem($menuItem->id, 2);

        $this->assertEquals($menuItem->id, $result['menu_item_id']);
        $this->assertEquals(2, $result['quantity']);
        $this->assertEquals(3000, $result['item_total']);
    }

    /** @test */
    public function can_add_item_with_options()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1500,
        ]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'single',
        ]);

        $option = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price_adjustment' => 200,
        ]);

        $result = $this->cartService->addItem(
            $menuItem->id,
            1,
            [$optionGroup->id => $option->id]
        );

        $this->assertEquals(200, $result['options_total']);
        $this->assertEquals(1700, $result['item_total']);
    }

    /** @test */
    public function can_update_cart_item_quantity()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000,
        ]);

        $item = $this->cartService->addItem($menuItem->id, 1);
        $this->cartService->updateQuantity($item['id'], 3);

        $cart = $this->cartService->getCart();
        $this->assertEquals(3, $cart[$item['id']]['quantity']);
        $this->assertEquals(3000, $cart[$item['id']]['item_total']);
    }

    /** @test */
    public function can_remove_item_from_cart()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $item = $this->cartService->addItem($menuItem->id);
        $this->cartService->removeItem($item['id']);

        $cart = $this->cartService->getCart();
        $this->assertEmpty($cart);
    }

    /** @test */
    public function can_clear_entire_cart()
    {
        $category = Category::factory()->create();
        $menuItem1 = MenuItem::factory()->create(['category_id' => $category->id]);
        $menuItem2 = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem1->id);
        $this->cartService->addItem($menuItem2->id);

        $this->cartService->clearCart();

        $cart = $this->cartService->getCart();
        $this->assertEmpty($cart);
    }

    /** @test */
    public function calculates_cart_totals_correctly()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        $totals = $this->cartService->getCartTotals();

        $this->assertEquals(2000, $totals['subtotal']);
        $this->assertEquals(200, $totals['tax']); // 10%
        $this->assertEquals(2200, $totals['total']);
        $this->assertEquals(2, $totals['item_count']);
    }

    /** @test */
    public function validates_required_options()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'is_required' => true,
            'name' => 'Size',
        ]);

        Option::factory()->create(['option_group_id' => $optionGroup->id]);

        $errors = $this->cartService->validateOptions($menuItem->fresh(), []);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Size is required', $errors[0]);
    }

    /** @test */
    public function validates_max_selections()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $optionGroup = OptionGroup::factory()->create([
            'menu_item_id' => $menuItem->id,
            'type' => 'multiple',
            'max_selections' => 2,
            'name' => 'Toppings',
        ]);

        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option3 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        $errors = $this->cartService->validateOptions(
            $menuItem->fresh(),
            [$optionGroup->id => [$option1->id, $option2->id, $option3->id]]
        );

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('maximum 2', $errors[0]);
    }

    /** @test */
    public function get_cart_count_returns_total_items()
    {
        $category = Category::factory()->create();
        $menuItem1 = MenuItem::factory()->create(['category_id' => $category->id]);
        $menuItem2 = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem1->id, 2);
        $this->cartService->addItem($menuItem2->id, 3);

        $this->assertEquals(5, $this->cartService->getCartCount());
    }
}
