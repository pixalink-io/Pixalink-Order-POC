<?php

namespace App\Livewire;

use App\Models\MenuItem;
use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;

class AddToCartModal extends Component
{
    public bool $isOpen = false;
    public ?MenuItem $menuItem = null;
    public int $quantity = 1;
    public array $selectedOptions = [];
    public string $specialInstructions = '';
    public array $errors = [];
    public bool $isAdding = false;

    protected function rules()
    {
        return [
            'quantity' => 'required|integer|min:1|max:99',
            'specialInstructions' => 'nullable|string|max:500',
        ];
    }

    #[On('openAddToCartModal')]
    public function openAddToCartModal($menuItemId)
    {
        try {
            $this->menuItem = MenuItem::with(['category', 'optionGroups.options'])->find($menuItemId);

            if (!$this->menuItem) {
                session()->flash('error', 'Menu item not found');
                return;
            }

            $this->resetForm();
            $this->preselectDefaultOptions();
            $this->isOpen = true;
        } catch (\Exception $e) {
            logger()->error('Failed to open add to cart modal', [
                'menu_item_id' => $menuItemId,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Unable to load menu item');
        }
    }

    protected function resetForm()
    {
        $this->quantity = 1;
        $this->selectedOptions = [];
        $this->specialInstructions = '';
        $this->errors = [];
        $this->isAdding = false;
    }

    protected function preselectDefaultOptions()
    {
        foreach ($this->menuItem->optionGroups as $group) {
            if ($group->is_required && $group->type === 'single' && $group->options->count() === 1) {
                $this->selectedOptions[$group->id] = $group->options->first()->id;
            }
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['menuItem', 'quantity', 'selectedOptions', 'specialInstructions', 'errors', 'isAdding']);
    }

    public function incrementQuantity()
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    /**
     * Handle option selection for both single and multiple selection types
     */
    public function selectOption($groupId, $optionId)
    {
        $group = $this->menuItem->optionGroups->firstWhere('id', $groupId);

        if (!$group) {
            return;
        }

        if ($group->type === 'single') {
            // Single selection (radio) - replace the selection
            $this->selectedOptions[$groupId] = $optionId;
        } else {
            // Multiple selection (checkbox) - toggle the selection
            if (!isset($this->selectedOptions[$groupId])) {
                $this->selectedOptions[$groupId] = [];
            }

            $key = array_search($optionId, $this->selectedOptions[$groupId]);

            if ($key !== false) {
                // Option already selected, remove it
                unset($this->selectedOptions[$groupId][$key]);
                $this->selectedOptions[$groupId] = array_values($this->selectedOptions[$groupId]); // Re-index
            } else {
                // Check max selections limit before adding
                if ($group->max_selections && count($this->selectedOptions[$groupId]) >= $group->max_selections) {
                    $this->errors = ["You can select up to {$group->max_selections} option(s) for {$group->name}"];
                    return;
                }

                // Add the option
                $this->selectedOptions[$groupId][] = $optionId;
            }

            // Clean up empty arrays
            if (empty($this->selectedOptions[$groupId])) {
                unset($this->selectedOptions[$groupId]);
            }
        }

        // Clear errors when user makes a selection
        $this->errors = [];
    }

    /**
     * Check if an option is selected
     */
    public function isOptionSelected($groupId, $optionId): bool
    {
        if (!isset($this->selectedOptions[$groupId])) {
            return false;
        }

        $selected = $this->selectedOptions[$groupId];

        if (is_array($selected)) {
            return in_array($optionId, $selected);
        }

        return $selected == $optionId;
    }

    /**
     * Calculate item total with options
     */
    #[Computed]
    public function itemTotal()
    {
        if (!$this->menuItem) {
            return 0;
        }

        $basePrice = $this->menuItem->price;
        $optionsTotal = 0;

        foreach ($this->selectedOptions as $groupId => $optionIds) {
            if (is_array($optionIds)) {
                foreach ($optionIds as $optionId) {
                    $option = \App\Models\Option::find($optionId);
                    if ($option) {
                        $optionsTotal += $option->price_adjustment;
                    }
                }
            } else {
                $option = \App\Models\Option::find($optionIds);
                if ($option) {
                    $optionsTotal += $option->price_adjustment;
                }
            }
        }

        return ($basePrice + $optionsTotal) * $this->quantity;
    }

    public function addToCart(CartService $cartService)
    {
        if (!$this->menuItem || $this->isAdding) {
            return;
        }

        $this->validate();

        $this->errors = $cartService->validateOptions($this->menuItem, $this->selectedOptions);

        if (!empty($this->errors)) {
            return;
        }

        $this->isAdding = true;

        try {
            $cartService->addItem(
                $this->menuItem->id,
                $this->quantity,
                $this->selectedOptions,
                $this->specialInstructions ?: null
            );

            $this->dispatch('cartUpdated');

            session()->flash('cart-message', "{$this->menuItem->name} added to cart!");

            $this->closeModal();
        } catch (\Exception $e) {
            logger()->error('Failed to add item to cart', [
                'menu_item_id' => $this->menuItem->id,
                'error' => $e->getMessage()
            ]);
            session()->flash('error', 'Failed to add item to cart');
        } finally {
            $this->isAdding = false;
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart-modal');
    }
}
