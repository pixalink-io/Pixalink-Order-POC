<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\On;

class CartDrawer extends Component
{
    public bool $isOpen = false;
    public array $cart = [];
    public array $totals = [];

    #[On('cartUpdated')]
    public function loadCart(CartService $cartService)
    {
        $this->cart = $cartService->getCart();
        $this->totals = $cartService->getCartTotals();

        $this->dispatch('cartCountUpdated', count: $this->totals['item_count']);
    }

    #[On('openCart')]
    public function openDrawer()
    {
        $this->isOpen = true;
    }

    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function updateQuantity(CartService $cartService, string $cartItemId, int $quantity)
    {
        if ($quantity < 1) {
            $this->removeItem($cartService, $cartItemId);
            return;
        }

        try {
            $cartService->updateQuantity($cartItemId, $quantity);
            $this->loadCart($cartService);
        } catch (\Exception $e) {
            session()->flash('cart-error', 'Failed to update quantity');
        }
    }

    public function removeItem(CartService $cartService, string $cartItemId)
    {
        try {
            $cartService->removeItem($cartItemId);
            $this->loadCart($cartService);
            session()->flash('cart-message', 'Item removed from cart');
        } catch (\Exception $e) {
            session()->flash('cart-error', 'Failed to remove item');
        }
    }

    public function clearCart(CartService $cartService)
    {
        if (empty($this->cart)) {
            return;
        }

        try {
            $cartService->clearCart();
            $this->loadCart($cartService);
            session()->flash('cart-message', 'Cart cleared');
        } catch (\Exception $e) {
            session()->flash('cart-error', 'Failed to clear cart');
        }
    }

    public function render()
    {
        return view('livewire.cart-drawer');
    }
}
