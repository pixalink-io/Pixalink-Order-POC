<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\On;

class CartIcon extends Component
{
    public int $cartCount = 0;

    #[On('cartUpdated')]
    #[On('cartCountUpdated')]
    public function updateCartCount(CartService $cartService)
    {
        $this->cartCount = $cartService->getCartCount();
    }

    public function mount(CartService $cartService)
    {
        $this->cartCount = $cartService->getCartCount();
    }

    public function openCart()
    {
        $this->dispatch('openCart');
    }

    public function render()
    {
        return view('livewire.cart-icon');
    }
}
