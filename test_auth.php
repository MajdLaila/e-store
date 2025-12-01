<?php

// Simple test script to verify authentication system
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;

echo "Testing Authentication System...\n\n";

// Test 1: User Model
echo "1. Testing User Model...\n";
try {
    $user = new User([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password123')
    ]);
    echo "✓ User model created successfully\n";
} catch (Exception $e) {
    echo "✗ User model error: " . $e->getMessage() . "\n";
}

// Test 2: EmailOtp Model
echo "\n2. Testing EmailOtp Model...\n";
try {
    $otp = EmailOtp::createForEmail('test@example.com');
    echo "✓ OTP created successfully: " . $otp->code . "\n";
} catch (Exception $e) {
    echo "✗ EmailOtp error: " . $e->getMessage() . "\n";
}

// Test 3: OTP Verification
echo "\n3. Testing OTP Verification...\n";
try {
    $isValid = EmailOtp::verify('test@example.com', $otp->code);
    echo $isValid ? "✓ OTP verification successful\n" : "✗ OTP verification failed\n";
} catch (Exception $e) {
    echo "✗ OTP verification error: " . $e->getMessage() . "\n";
}

echo "\nAuthentication system test completed!\n";
