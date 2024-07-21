<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('categories.index');
});

Route::resource('categories',CategoryController::class);
Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
