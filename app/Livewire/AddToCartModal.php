<?php

namespace App\Livewire;

use App\Models\MenuItem;
use App\Services\CartService;
use Livewire\Component;

class AddToCartModal extends Component
{
    public $isOpen = false;
    public $menuItem;
    public $quantity = 1;
    public $selectedOptions = [];
    public $specialInstructions = '';
    public $errors = [];

    protected $listeners = ['openAddToCartModal'];

    public function openAddToCartModal($menuItemId)
    {
        $this->menuItem = MenuItem::with(['category', 'optionGroups.options'])->find($menuItemId);

        if (!$this->menuItem) {
            return;
        }

        $this->quantity = 1;
        $this->selectedOptions = [];
        $this->specialInstructions = '';
        $this->errors = [];

        foreach ($this->menuItem->optionGroups as $group) {
            if ($group->is_required && $group->type === 'single' && $group->options->count() === 1) {
                $this->selectedOptions[$group->id] = $group->options->first()->id;
            }
        }

        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['menuItem', 'quantity', 'selectedOptions', 'specialInstructions', 'errors']);
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(CartService $cartService)
    {
        if (!$this->menuItem) {
            return;
        }

        $this->errors = $cartService->validateOptions($this->menuItem, $this->selectedOptions);

        if (!empty($this->errors)) {
            return;
        }

        $cartService->addItem(
            $this->menuItem->id,
            $this->quantity,
            $this->selectedOptions,
            $this->specialInstructions ?: null
        );

        $this->dispatch('cartUpdated');

        session()->flash('cart-message', "{$this->menuItem->name} added to cart!");

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.add-to-cart-modal');
    }
}
