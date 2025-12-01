<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Ads\AdsController;

Route::prefix('ads')
  ->as('ads.')
  ->middleware('auth:sanctum') // <-- هنا حماية التوكن

  ->group(static function (): void {
    // CRUD operations for Ads
    Route::get('/', [AdsController::class, 'index'])->name('index'); // Get all ads (with pagination/filter options if implemented)
    Route::post('/', [AdsController::class, 'store'])->name('store'); // Create new ad
    Route::get('{ad}', [AdsController::class, 'show'])->name('show'); // Get single ad by ID
    Route::put('{ad}', [AdsController::class, 'update'])->name('update'); // Update existing ad
    Route::delete('{ad}', [AdsController::class, 'destroy'])->name('destroy'); // Delete ad
  });
