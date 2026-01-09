<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CheckoutController;


Route::get('/', function () {
    return redirect('/menu');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menuItem}', [MenuController::class, 'show'])->name('menu.show');

// Checkout routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
