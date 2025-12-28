<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Order\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('orders')
  ->middleware('auth:sanctum')
  ->as('orders.')
  ->group(function (): void {

    // Create order
    Route::post('/', [OrderController::class, 'store'])
      ->name('store');
  Route::get('/', [OrderController::class, 'index']); // كل الأوردرات
  Route::get('/user/{userId}', [OrderController::class, 'getUserOrders']); // أوردرات يوزر
  Route::patch('/{orderId}/status', [OrderController::class, 'updateStatus']); // تعديل الحالة
  Route::delete('/{orderId}', [OrderController::class, 'destroy']); // ح
});
