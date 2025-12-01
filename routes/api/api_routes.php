<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group(['as' => 'api.'], function (): void {
    Route::prefix('v1')->as('v1.')->group(static function (): void {
        require __DIR__ . '/v1/api_v1_routes.php';
    });
 
});
