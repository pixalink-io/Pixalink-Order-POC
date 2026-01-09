<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(CartService $cartService)
    {
        $cart = $cartService->getCart();

        // Redirect if cart is empty
        if (empty($cart)) {
            return redirect()->route('menu.index')
                ->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        $totals = $cartService->getCartTotals();

        return view('checkout.index', compact('cart', 'totals'));
    }

    public function store(Request $request, CartService $cartService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = $cartService->getCart();

        if (empty($cart)) {
            return redirect()->route('menu.index')
                ->with('error', 'Your cart is empty.');
        }

        // TODO: Create order in database
        // TODO: Process payment if needed
        // TODO: Send confirmation email

        // Clear cart after successful order
        $cartService->clearCart();

        return redirect()->route('checkout.success')
            ->with('success', 'Your order has been placed successfully!');
    }
}
