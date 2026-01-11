<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Livewire\Checkout\CheckoutPage;
use App\Livewire\Checkout\OrderConfirmation;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartService = new CartService();
    }

    /** @test */
    public function checkout_page_redirects_if_cart_is_empty()
    {
        $this->cartService->clearCart();

        Livewire::test(CheckoutPage::class)
            ->assertRedirect(route('menu.index'));

        $this->assertEquals('Your cart is empty.', session('error'));
    }

    /** @test */
    public function checkout_page_loads_with_cart_items()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        Livewire::test(CheckoutPage::class)
            ->assertSet('cartItems', function ($cartItems) {
                return count($cartItems) === 1;
            })
            ->assertSee('Test Item');
    }

    /** @test */
    public function customer_name_is_required()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', '')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertHasErrors(['customerName' => 'required']);
    }

    /** @test */
    public function customer_name_must_be_at_least_2_characters()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'A')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertHasErrors(['customerName' => 'min']);
    }

    /** @test */
    public function customer_name_cannot_exceed_100_characters()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', str_repeat('a', 101))
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertHasErrors(['customerName' => 'max']);
    }

    /** @test */
    public function phone_number_is_optional()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('customerPhone', '')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertHasNoErrors(['customerPhone']);
    }

    /** @test */
    public function phone_number_must_be_valid_malaysian_format()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('customerPhone', 'invalid-phone')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertHasErrors(['customerPhone' => 'regex']);
    }

    /** @test */
    public function valid_malaysian_phone_numbers_are_accepted()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $validPhones = ['0123456789', '60123456789', '+60123456789'];

        foreach ($validPhones as $phone) {
            // Add item to cart for each test iteration
            $this->cartService->addItem($menuItem->id);

            Livewire::test(CheckoutPage::class)
                ->set('customerName', 'John Doe')
                ->set('customerPhone', $phone)
                ->set('tableNumber', 'A5')
                ->call('placeOrder')
                ->assertHasNoErrors(['customerPhone']);
        }
    }

    /** @test */
    public function table_number_is_required()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', '')
            ->call('placeOrder')
            ->assertHasErrors(['tableNumber' => 'required']);
    }

    /** @test */
    public function table_number_cannot_exceed_10_characters()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', str_repeat('A', 11))
            ->call('placeOrder')
            ->assertHasErrors(['tableNumber' => 'max']);
    }

    /** @test */
    public function notes_are_optional()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->set('notes', '')
            ->call('placeOrder')
            ->assertHasNoErrors(['notes']);
    }

    /** @test */
    public function notes_cannot_exceed_500_characters()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);
        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->set('notes', str_repeat('a', 501))
            ->call('placeOrder')
            ->assertHasErrors(['notes' => 'max']);
    }

    /** @test */
    public function order_is_created_successfully_with_valid_data()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        $this->assertCount(0, Order::all());

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('customerPhone', '0123456789')
            ->set('tableNumber', 'A5')
            ->set('notes', 'Extra napkins')
            ->call('placeOrder');

        $this->assertCount(1, Order::all());

        $order = Order::first();
        $this->assertEquals('John Doe', $order->customer_name);
        $this->assertEquals('0123456789', $order->customer_phone);
        $this->assertEquals('A5', $order->table_number);
        $this->assertEquals('Extra napkins', $order->notes);
        $this->assertEquals('pending', $order->status);
    }

    /** @test */
    public function order_items_are_created_with_correct_data()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $order = Order::first();
        $this->assertCount(1, $order->orderItems);

        $orderItem = $order->orderItems->first();
        $this->assertEquals($menuItem->id, $orderItem->menu_item_id);
        $this->assertEquals('Test Item', $orderItem->menu_item_name);
        $this->assertEquals(1000, $orderItem->menu_item_price);
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(2000, $orderItem->item_total);
    }

    /** @test */
    public function order_totals_are_calculated_correctly()
    {
        $category = Category::factory()->create();
        $menuItem1 = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000, // $10.00
        ]);
        $menuItem2 = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1500, // $15.00
        ]);

        $this->cartService->addItem($menuItem1->id, 1); // $10.00
        $this->cartService->addItem($menuItem2->id, 2); // $30.00

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $order = Order::first();
        $this->assertEquals(4000, $order->subtotal); // $40.00
        $this->assertEquals(400, $order->tax); // 10% = $4.00
        $this->assertEquals(4400, $order->total); // $44.00
    }

    /** @test */
    public function cart_is_cleared_after_successful_order()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem->id);
        $this->assertNotEmpty($this->cartService->getCart());

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $this->assertEmpty($this->cartService->getCart());
    }

    /** @test */
    public function redirects_to_confirmation_page_after_successful_order()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertRedirect();

        $order = Order::first();
        $this->assertNotNull($order);
    }

    /** @test */
    public function cart_updated_event_is_dispatched_after_order()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder')
            ->assertDispatched('cartUpdated');
    }

    /** @test */
    public function order_confirmation_page_displays_order_details()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'name' => 'Test Item',
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $order = Order::first();

        Livewire::test(OrderConfirmation::class, ['order' => $order])
            ->assertSee('Order Placed Successfully')
            ->assertSee($order->order_number)
            ->assertSee('John Doe')
            ->assertSee('A5')
            ->assertSee('Test Item');
    }

    /** @test */
    public function order_confirmation_page_shows_correct_totals()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 1000,
        ]);

        $this->cartService->addItem($menuItem->id, 2);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $order = Order::first();

        Livewire::test(OrderConfirmation::class, ['order' => $order])
            ->assertSee(number_format($order->subtotal, 2))
            ->assertSee(number_format($order->tax, 2))
            ->assertSee(number_format($order->total, 2));
    }

    /** @test */
    public function prevents_duplicate_submission()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem->id);

        $component = Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->set('isSubmitting', true)
            ->call('placeOrder');

        // Should not create order if already submitting
        $this->assertCount(0, Order::all());
    }

    /** @test */
    public function order_number_is_unique()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        // Create first order
        $this->cartService->addItem($menuItem->id);
        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $firstOrderNumber = Order::first()->order_number;

        // Wait a tiny bit to ensure different timestamp/random
        usleep(100000); // 0.1 second

        // Create second order
        $this->cartService->clearCart(); // Clear first
        $this->cartService->addItem($menuItem->id);
        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'Jane Doe')
            ->set('tableNumber', 'B3')
            ->call('placeOrder');

        $secondOrderNumber = Order::skip(1)->first()->order_number;

        $this->assertNotEquals($firstOrderNumber, $secondOrderNumber);
        $this->assertCount(2, Order::all());
    }

    /** @test */
    public function order_has_placed_at_timestamp()
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->create(['category_id' => $category->id]);

        $this->cartService->addItem($menuItem->id);

        Livewire::test(CheckoutPage::class)
            ->set('customerName', 'John Doe')
            ->set('tableNumber', 'A5')
            ->call('placeOrder');

        $order = Order::first();
        $this->assertNotNull($order->placed_at);
    }
}
