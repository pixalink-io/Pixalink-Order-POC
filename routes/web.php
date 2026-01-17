<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CheckoutController;
use App\Livewire\Checkout\CheckoutPage;
use App\Livewire\Checkout\OrderConfirmation;
use App\Livewire\Kitchen\KitchenDisplay;

Route::get('/', function () {
    return redirect('/menu');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menuItem}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/kitchen-display', KitchenDisplay::class)->name('kitchen.display');
// Checkout routes - USING LIVEWIRE (NEW)
Route::get('/checkout', CheckoutPage::class)->name('checkout.index');
Route::get('/order/{order:order_number}/confirmation', OrderConfirmation::class)->name('order.confirmation');

