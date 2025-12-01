<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FailedLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'ip_address',
        'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    /**
     * Record a failed login attempt
     */
    public static function record(string $email = null, string $ipAddress = null): self
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress,
            'attempted_at' => now(),
        ]);
    }

    /**
     * Check if IP address has too many failed attempts
     */
    public static function hasTooManyAttempts(string $ipAddress, int $maxAttempts = 5, int $decayMinutes = 15): bool
    {
        $attempts = self::where('ip_address', $ipAddress)
            ->where('attempted_at', '>=', now()->subMinutes($decayMinutes))
            ->count();

        return $attempts >= $maxAttempts;
    }

    /**
     * Check if email has too many failed attempts
     */
    public static function hasTooManyEmailAttempts(string $email, int $maxAttempts = 3, int $decayMinutes = 15): bool
    {
        $attempts = self::where('email', $email)
            ->where('attempted_at', '>=', now()->subMinutes($decayMinutes))
            ->count();

        return $attempts >= $maxAttempts;
    }
}
