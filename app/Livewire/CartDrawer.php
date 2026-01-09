<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;

class CartDrawer extends Component
{
    public $isOpen = false;
    public $cart = [];
    public $totals = [];

    protected $listeners = ['cartUpdated' => 'loadCart', 'openCart' => 'openDrawer'];

    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    public function loadCart(CartService $cartService)
    {
        $this->cart = $cartService->getCart();
        $this->totals = $cartService->getCartTotals();

        $this->dispatch('cartCountUpdated', count: $this->totals['item_count']);
    }

    public function openDrawer()
    {
        $this->isOpen = true;
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function updateQuantity(CartService $cartService, string $cartItemId, int $quantity)
    {
        $cartService->updateQuantity($cartItemId, $quantity);
        $this->loadCart($cartService);
    }

    public function removeItem(CartService $cartService, string $cartItemId)
    {
        $cartService->removeItem($cartItemId);
        $this->loadCart($cartService);

        session()->flash('cart-message', 'Item removed from cart');
    }

    public function clearCart(CartService $cartService)
    {
        $cartService->clearCart();
        $this->loadCart($cartService);

        session()->flash('cart-message', 'Cart cleared');
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
