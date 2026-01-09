<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const CART_SESSION_KEY = 'shopping_cart';

    /**
     * Get all cart items
     */
    public function getCart(): array
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Get cart item count
     */
    public function getCartCount(): int
    {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Add item to cart
     */
    public function addItem(int $menuItemId, int $quantity = 1, array $selectedOptions = [], ?string $specialInstructions = null): array
    {
        $menuItem = MenuItem::with(['category', 'optionGroups.options'])->findOrFail($menuItemId);

        $cart = $this->getCart();
        $cartItemId = $this->generateCartItemId($menuItemId, $selectedOptions);

        // Calculate options total
        $optionsTotal = $this->calculateOptionsTotal($selectedOptions);

        // Calculate item total
        $itemPrice = $menuItem->price + $optionsTotal;
        $itemTotal = $itemPrice * $quantity;

        if (isset($cart[$cartItemId])) {
            // Item exists, update quantity
            $cart[$cartItemId]['quantity'] += $quantity;
            $cart[$cartItemId]['item_total'] = ($menuItem->price + $optionsTotal) * $cart[$cartItemId]['quantity'];
        } else {
            // New item
            $cart[$cartItemId] = [
                'id' => $cartItemId,
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'image' => $menuItem->image,
                'category' => $menuItem->category->name,
                'quantity' => $quantity,
                'selected_options' => $selectedOptions,
                'options_total' => $optionsTotal,
                'item_total' => $itemTotal,
                'special_instructions' => $specialInstructions,
            ];
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart[$cartItemId];
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(string $cartItemId, int $quantity): void
    {
        $cart = $this->getCart();

        if (isset($cart[$cartItemId])) {
            if ($quantity <= 0) {
                unset($cart[$cartItemId]);
            } else {
                $cart[$cartItemId]['quantity'] = $quantity;
                $itemPrice = $cart[$cartItemId]['price'] + $cart[$cartItemId]['options_total'];
                $cart[$cartItemId]['item_total'] = $itemPrice * $quantity;
            }

            Session::put(self::CART_SESSION_KEY, $cart);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(string $cartItemId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$cartItemId])) {
            unset($cart[$cartItemId]);
            Session::put(self::CART_SESSION_KEY, $cart);
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): void
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    /**
     * Get cart totals
     */
    public function getCartTotals(): array
    {
        $cart = $this->getCart();

        $subtotal = array_sum(array_column($cart, 'item_total'));
        $tax = $subtotal * 0.10; // 10% tax
        $total = $subtotal + $tax;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2),
            'item_count' => $this->getCartCount(),
        ];
    }

    /**
     * Generate unique cart item ID based on menu item and options
     */
    private function generateCartItemId(int $menuItemId, array $selectedOptions): string
    {
        ksort($selectedOptions);
        $optionsString = json_encode($selectedOptions);
        return md5($menuItemId . $optionsString);
    }

    /**
     * Calculate total price adjustment from selected options
     */
    private function calculateOptionsTotal(array $selectedOptions): float
    {
        $total = 0;

        foreach ($selectedOptions as $groupId => $optionIds) {
            if (is_array($optionIds)) {
                foreach ($optionIds as $optionId) {
                    $option = \App\Models\Option::find($optionId);
                    if ($option) {
                        $total += $option->price_adjustment;
                    }
                }
            } else {
                $option = \App\Models\Option::find($optionIds);
                if ($option) {
                    $total += $option->price_adjustment;
                }
            }
        }

        return $total;
    }

    /**
     * Validate selected options against menu item requirements
     */
    public function validateOptions(MenuItem $menuItem, array $selectedOptions): array
    {
        $errors = [];

        foreach ($menuItem->optionGroups as $group) {
            $groupOptions = $selectedOptions[$group->id] ?? [];

            if (!is_array($groupOptions)) {
                $groupOptions = [$groupOptions];
            }

            $selectedCount = count(array_filter($groupOptions));

            // Check required
            if ($group->is_required && $selectedCount === 0) {
                $errors[] = "{$group->name} is required";
            }

            // Check min selections
            if ($group->min_selections > 0 && $selectedCount < $group->min_selections) {
                $errors[] = "{$group->name} requires at least {$group->min_selections} selection(s)";
            }

            // Check max selections
            if ($group->max_selections && $selectedCount > $group->max_selections) {
                $errors[] = "{$group->name} allows maximum {$group->max_selections} selection(s)";
            }
        }

        return $errors;
    }
}
