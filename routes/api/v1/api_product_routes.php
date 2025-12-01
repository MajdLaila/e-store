<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Product\ProductController;

// جميع المسارات التي تخص المنتجات (products)
Route::prefix('products')
  ->middleware('auth:sanctum') // <-- هنا حماية التوكن

  ->as('products.')
    ->group(static function (): void {
        // GET    /api/v1/products         جلب كل المنتجات (مع تصفية/تصفح إذا مطبقة)
        Route::get('/', [ProductController::class, 'index'])->name('index');
        // POST   /api/v1/products         إنشاء منتج جديد
        Route::post('/', [ProductController::class, 'store'])->name('store');
        // GET    /api/v1/products/{product}    جلب منتج محدد بواسطة المعرف
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        // PUT    /api/v1/products/{product}    تحديث منتج
        Route::patch('{product}', [ProductController::class, 'update'])->name('update');
        // DELETE /api/v1/products/{product}    حذف منتج
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');

     });

