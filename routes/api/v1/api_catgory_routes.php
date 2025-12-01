<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('categories')
  ->as('categories.')
  ->middleware('auth:sanctum') // <-- هنا حماية التوكن

  ->group(static function (): void {
    // CRUD operations
    Route::get('/', [CategoryController::class, 'index'])->name('index'); // Get all categories (with pagination/filter options if implemented)
    Route::post('/', [CategoryController::class, 'store'])->name('store'); // Create new category
    Route::get('{category}', [CategoryController::class, 'show'])->name('show'); // Get single category by ID
    Route::put('{category}', [CategoryController::class, 'update'])->name('update'); // Update existing category
    Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy'); // Delete category

    // Get children of a category
    Route::get('{category}/children', [CategoryController::class, 'children'])->name('children');

    // Get products of a category
    Route::get('{category}/products', [CategoryController::class, 'products'])->name('products');
  });
