<?php

namespace App\Livewire\Checkout;

use App\Models\Order;
use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]

class CheckoutPage extends Component
{
    public array $cartItems = [];
    public array $totals = [];

    public string $customerName = '';
    public string $customerPhone = '';
    public string $tableNumber = '';
    public string $notes = '';

    public bool $isSubmitting = false;

    protected function rules()
    {
        return [
            'customerName' => 'required|min:2|max:100',
            'customerPhone' => 'nullable|regex:/^(\+?6?01)[0-9]{8,9}$/',
            'tableNumber' => 'required|max:10',
            'notes' => 'nullable|max:500',
        ];
    }

    protected $messages = [
        'customerName.required' => 'Please enter your name',
        'customerName.min' => 'Name must be at least 2 characters',
        'customerPhone.regex' => 'Please enter a valid Malaysian phone number',
        'tableNumber.required' => 'Please enter your table number',
    ];

    public function mount(CartService $cartService)
    {
        $this->cartItems = $cartService->getCart();
        $this->totals = $cartService->getCartTotals();

        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty.');
            return redirect()->route('menu.index');
        }
    }

    public function placeOrder(CartService $cartService)
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->validate();
        $this->isSubmitting = true;

        try {
            $order = Order::createFromCart($this->cartItems, [
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone ?: null,
                'table_number' => $this->tableNumber,
                'notes' => $this->notes ?: null,
            ]);

            $cartService->clearCart();
            $this->dispatch('cartUpdated');

            return redirect()->route('order.confirmation', ['order' => $order->order_number]);
        } catch (\Exception $e) {
            logger()->error('Failed to place order', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to place order. Please try again.');
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.checkout.checkout-page');
    }
}
