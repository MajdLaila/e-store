<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
  public const STATUS_PENDING    = 'pending';
  public const STATUS_PROCESSING = 'processing';
  public const STATUS_SHIPPED    = 'shipped';
  public const STATUS_DELIVERED  = 'delivered';
  public const STATUS_CANCELLED  = 'cancelled';

  protected $fillable = [
    'user_id',
    'name',
    'description',
    'total_price',
    'status',
    'shipping_address',
    'phone',
  ];

  protected $casts = [
    'total_price' => 'decimal:2',
  ];

  /* ================= Relationships ================= */

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }
}
