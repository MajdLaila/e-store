<?php

declare(strict_types=1);

namespace App\Http\Repositories\Eloquent;

use App\Models\EmailOtp;
use Carbon\Carbon;

final readonly class OtpRepository
{
    public function __construct(
        private EmailOtp $emailOtp,
    ) {}

    /**
     * Create a new OTP record
     */
    public function createOtp(string $email, int $code, Carbon $expiresAt): EmailOtp
    {
        return $this->emailOtp->create([
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiresAt,
            'used' => false,
        ]);
    }

    /**
     * Delete all OTPs for an email
     */
    public function deleteOtpByEmail(string $email): bool
    {
        return $this->emailOtp->where('email', $email)->delete() > 0;
    }

    /**
     * Find valid OTP by email and code
     */
    public function findValidOtp(string $email, string $code): ?EmailOtp
    {
        return $this->emailOtp
            ->where('email', $email)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Mark OTP as used
     */
    public function markOtpAsUsed(EmailOtp $otp): bool
    {
        return $otp->update(['used' => true]);
    }

    /**
     * Check if email has recent OTP (within last minute)
     */
    public function hasRecentOtp(string $email): bool
    {
        return $this->emailOtp
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subMinute())
            ->exists();
    }

    /**
     * Get OTP count for email in last hour
     */
    public function getOtpCountInLastHour(string $email): int
    {
        return $this->emailOtp
            ->where('email', $email)
            ->where('created_at', '>', Carbon::now()->subHour())
            ->count();
    }
}
