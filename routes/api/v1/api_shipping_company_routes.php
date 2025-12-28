<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ShippingCompanies\ShippingCompaniesController;
use Illuminate\Support\Facades\Route;


Route::prefix('ShippingCompany')
  ->as('ShippingCompany.')
  ->middleware('auth:sanctum') // <-- هنا حماية التوكن

  ->group(static function (): void {
     Route::get('/', [ShippingCompaniesController::class, 'index'])->name('index');
    Route::post('/', [ShippingCompaniesController::class, 'store'])->name('store');
    Route::put('{shipping_company_id}', [ShippingCompaniesController::class, 'update'])->name('update');
    Route::delete('{shipping_company_id}', [ShippingCompaniesController::class, 'destroy'])->name('destroy');
  });
