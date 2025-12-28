<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Apply CORS middleware to all API routes
Route::middleware('api',)->group(function (): void {
  require_once __DIR__ . '/api_auth_routes.php';
  require_once __DIR__ . '/api_user_routes.php';
  require_once __DIR__ . '/api_catgory_routes.php';
  require_once __DIR__ . '/api_product_routes.php';
  require_once __DIR__ . '/api_ads_routes.php';
  require_once __DIR__ . '/api_order_routes.php';
  require_once __DIR__ . '/api_shipping_company_routes.php';

});
