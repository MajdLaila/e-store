<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::prefix('auth')
    ->as('auth.')
    ->group(static function (): void {
        Route::prefix('user')
            ->as('user.')
            ->group(static function (): void {
                Route::post('login', [AuthController::class, 'login'])->name('login');
                Route::post('register', [AuthController::class, 'register'])->name('register');
            });

        // OTP routes
        Route::post('send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
        Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
        
        // Forgot Password routes
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
    });
