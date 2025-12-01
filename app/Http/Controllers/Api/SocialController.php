<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLoginRequest;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    /**
     * Handle Google login/register
     */
    public function googleCallback(SocialLoginRequest $request): JsonResponse
    {
        try {
            return $this->handleSocialLogin('google', $request->all());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Google login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle Facebook login/register
     */
    public function facebookCallback(SocialLoginRequest $request): JsonResponse
    {
        try {
            return $this->handleSocialLogin('facebook', $request->all());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Facebook login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Handle social login/register logic
     */
    private function handleSocialLogin(string $provider, array $data): JsonResponse
    {
        try {
            $providerIdField = $provider . '_id';
            $providerId = $data[$providerIdField];
            $email = $data['email'];
            $name = $data['name'];
            $avatar = $data['avatar'] ?? null;

            // Check if social account already exists
            $socialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($socialAccount) {
                // User exists, log them in
                $user = $socialAccount->user;
                
                if (!$user->isActive()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Account is deactivated'
                    ], 403);
                }

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'is_new_user' => false
                    ]
                ]);
            }

            // Check if user exists with this email
            $user = User::where('email', $email)->first();

            if ($user) {
                // User exists but doesn't have this social account linked
                // Link the social account to existing user
                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'profile' => [
                        'name' => $name,
                        'avatar' => $avatar,
                    ]
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Social account linked successfully',
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'token_type' => 'Bearer',
                        'is_new_user' => false
                    ]
                ]);
            }

            // Create new user and social account
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)), // Random password for social users
                'avatar' => $avatar,
                'email_verified_at' => now(), // Social accounts are considered verified
                'is_active' => true,
            ]);

            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
                'profile' => [
                    'name' => $name,
                    'avatar' => $avatar,
                ]
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Account created and login successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'is_new_user' => true
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Social login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Link social account to existing user
     */
    public function linkSocialAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'provider' => 'required|in:google,facebook',
                'provider_id' => 'required|string',
                'email' => 'required|email',
                'name' => 'required|string',
                'avatar' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $provider = $request->provider;
            $providerId = $request->provider_id;

            // Check if social account already exists
            $existingSocialAccount = SocialAccount::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($existingSocialAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'This social account is already linked to another user'
                ], 409);
            }

            // Check if user already has this provider linked
            $userSocialAccount = $user->socialAccounts()
                ->where('provider', $provider)
                ->first();

            if ($userSocialAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a ' . ucfirst($provider) . ' account linked'
                ], 409);
            }

            // Create social account
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
                'profile' => [
                    'name' => $request->name,
                    'avatar' => $request->avatar,
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' account linked successfully',
                'data' => [
                    'user' => $user->fresh(),
                    'linked_providers' => $user->socialAccounts->pluck('provider')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to link social account. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Unlink social account
     */
    public function unlinkSocialAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'provider' => 'required|in:google,facebook',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $provider = $request->provider;

            $socialAccount = $user->socialAccounts()
                ->where('provider', $provider)
                ->first();

            if (!$socialAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'No ' . ucfirst($provider) . ' account linked'
                ], 404);
            }

            // Check if user has a password (to prevent locking them out)
            if (!$user->password) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot unlink social account. Please set a password first.'
                ], 400);
            }

            $socialAccount->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' account unlinked successfully',
                'data' => [
                    'linked_providers' => $user->socialAccounts->pluck('provider')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink social account. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get user's linked social accounts
     */
    public function getLinkedAccounts(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $socialAccounts = $user->socialAccounts;

            return response()->json([
                'success' => true,
                'data' => [
                    'linked_accounts' => $socialAccounts,
                    'providers' => $socialAccounts->pluck('provider')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve linked accounts.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}
