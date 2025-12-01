<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpRequest;
use App\Http\Requests\SendOtpRequest;
use App\Models\EmailOtp;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class OtpController extends Controller
{
    /**
     * Send OTP to email for verification
     */
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            $email = $request->email;
            $purpose = $request->purpose;

            // Rate limiting for OTP requests
            $key = 'otp:' . $email;
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "Too many OTP requests. Please try again in {$seconds} seconds."
                ], 429);
            }

            // Check if user exists for login/reset password purposes
            if (in_array($purpose, ['login', 'reset_password'])) {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found with this email address'
                    ], 404);
                }
            }

            // Check if user already exists for register purpose
            if ($purpose === 'register') {
                $user = User::where('email', $email)->first();
                if ($user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User already exists with this email address'
                    ], 409);
                }
            }

            // Create OTP
            $otp = EmailOtp::createForEmail($email);

            // Send OTP email
            Mail::to($email)->send(new OtpMail($otp->code, $purpose));

            // Record rate limit
            RateLimiter::hit($key, 300); // 5 minutes

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your email',
                'data' => [
                    'email' => $email,
                    'expires_in' => 600, // 10 minutes in seconds
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp(OtpRequest $request): JsonResponse
    {
        try {
            $email = $request->email;
            $code = $request->code;
            $purpose = $request->purpose;

            // Rate limiting for OTP verification
            $key = 'otp_verify:' . $email;
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "Too many verification attempts. Please try again in {$seconds} seconds."
                ], 429);
            }

            // Verify OTP
            if (!EmailOtp::verify($email, $code)) {
                RateLimiter::hit($key, 300); // 5 minutes
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP code'
                ], 400);
            }

            // Handle different purposes
            switch ($purpose) {
                case 'register':
                    return $this->handleRegisterVerification($email);
                case 'login':
                    return $this->handleLoginVerification($email);
                case 'reset_password':
                    return $this->handleResetPasswordVerification($email);
                case 'verify_email':
                    return $this->handleEmailVerification($email);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid purpose'
                    ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle register verification
     */
    private function handleRegisterVerification(string $email): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You can now complete registration.',
            'data' => [
                'email' => $email,
                'verified' => true
            ]
        ]);
    }

    /**
     * Handle login verification
     */
    private function handleLoginVerification(string $email): JsonResponse
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * Handle reset password verification
     */
    private function handleResetPasswordVerification(string $email): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You can now reset your password.',
            'data' => [
                'email' => $email,
                'verified' => true
            ]
        ]);
    }

    /**
     * Handle email verification
     */
    private function handleEmailVerification(string $email): JsonResponse
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update(['email_verified_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'data' => [
                'user' => $user,
                'verified' => true
            ]
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            // Rate limiting for resend requests
            $key = 'otp_resend:' . $request->email;
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "Too many resend requests. Please try again in {$seconds} seconds."
                ], 429);
            }

            // Call sendOtp method
            $request->merge(['resend' => true]);
            return $this->sendOtp($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
