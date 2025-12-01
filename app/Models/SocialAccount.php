<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SocialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'profile',
    ];

    protected $casts = [
        'profile' => 'array',
    ];

    /**
     * Get the user that owns the social account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Find user by social provider
     */
    public static function findUserByProvider(string $provider, string $providerId): ?User
    {
        $socialAccount = self::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        return $socialAccount?->user;
    }

    /**
     * Create or update social account
     */
    public static function createOrUpdate(array $data): self
    {
        return self::updateOrCreate(
            [
                'provider' => $data['provider'],
                'provider_id' => $data['provider_id'],
            ],
            $data
        );
    }
}
