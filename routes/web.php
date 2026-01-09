<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;


Route::get('/', function () {
    return redirect('/menu');
});

Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
Route::get('/menu/{menuItem}', [MenuController::class, 'show'])->name('menu.show');
