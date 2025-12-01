<?php

declare(strict_types=1);

namespace App\Http\Services\Auth;

use App\Http\Repositories\Eloquent\AuthRepostrie;
use App\Http\Repositories\Eloquent\OtpRepository;
use App\Mail\OtpMail;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

final readonly class AuthService
{
  public function __construct(
    private AuthRepostrie $authRepostrie,
    private OtpRepository $otpRepository,
  ) {}

  /**
   * Authenticate a user.
   *
   * @param  array<string, mixed> $credentials
   * @return array<string, mixed>
   *
   * @throws ApiException
   */
  public function authuser(array $credentials): array
  {
    $user = $this->authRepostrie->findByEmail($credentials['email']);
    if (! $user) {
      throw new ApiException('User not found', 404);
    }
    if (! Hash::check($credentials['password'], $user->password)) {
      throw new ApiException('Invalid credentials', 400);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return [
      'user' => $user,
      'token' => $token,
      'token_type' => 'Bearer',
    ];
  }

  /**
   * Register a new user.
   *
   * @param array<string, mixed> $data
   * @return mixed
   *
   * @throws ApiException
   */
  public function registeruser(array $data)
  {
    try {
      return $this->authRepostrie->createuser($data);
    } catch (\Exception $e) {
      throw new ApiException('Registration failed: ' . $e->getMessage(), 500);
    }
  }

  /**
   * Send OTP code.
   *
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   *
   * @throws ApiException
   */
  public function sendOtpCode(array $data): array
  {
    $email = $data['email'];
    $purpose = $data['purpose'] ?? 'verify_email';

    if ($this->otpRepository->hasRecentOtp($email)) {
      throw new ApiException('Please wait before requesting another OTP', 429);
    }

    if ($this->otpRepository->getOtpCountInLastHour($email) >= 5) {
      throw new ApiException('Too many OTP requests. Please try again later', 429);
    }

    $otpCode = rand(100000, 999999);
    $this->otpRepository->deleteOtpByEmail($email);
    $this->otpRepository->createOtp($email, $otpCode, Carbon::now()->addMinutes(10));

    try {
      Mail::to($email)->send(new OtpMail((string)$otpCode, $purpose));
    } catch (\Exception $e) {
      throw new ApiException('Failed to send email: ' . $e->getMessage(), 500);
    }

    return [
      'email' => $email,
      'purpose' => $purpose,
      'message' => 'OTP sent successfully',
      'expires_in_minutes' => 10,
    ];
  }

  /**
   * Verify OTP code.
   *
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   *
   * @throws ApiException
   */
  public function verifyOtpCode(array $data): array
  {
    $email = $data['email'];
    $code = $data['code'];
    $purpose = $data['purpose'] ?? 'verify_email';

    $otp = $this->otpRepository->findValidOtp($email, $code);
    if (! $otp) {
      throw new ApiException('Invalid or expired OTP code', 400);
    }

    $this->otpRepository->markOtpAsUsed($otp);

    if ($purpose === 'register') {
      $user = $this->authRepostrie->findByEmail($email);
      if ($user) {
        $user->update(['email_verified_at' => Carbon::now()]);
      }
    }

    return [
      'email' => $email,
      'purpose' => $purpose,
      'message' => 'OTP verified successfully',
      'verified_at' => Carbon::now(),
    ];
  }
}
