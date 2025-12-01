<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'last_activity',
    ];

    protected $casts = [
        'last_activity' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create or update user session
     */
    public static function createOrUpdate(User $user, string $ipAddress = null, string $userAgent = null): self
    {
        return self::updateOrCreate(
            [
                'user_id' => $user->id,
                'ip_address' => $ipAddress,
            ],
            [
                'user_agent' => $userAgent,
                'last_activity' => now(),
            ]
        );
    }

    /**
     * Clean up old sessions
     */
    public static function cleanup(int $daysOld = 30): int
    {
        return self::where('last_activity', '<', now()->subDays($daysOld))->delete();
    }
}
