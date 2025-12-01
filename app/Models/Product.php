<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','description','category_id','is_valid','price','is_show','hot_price','stock'
    ];

    protected $casts = [
        'is_valid' => 'boolean',
        'is_show' => 'boolean',
        'hot_price' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function keys()
    {
        return $this->belongsToMany(ProductKey::class, 'product_value_product', 'product_id', 'product_value_id')
                    ->withTimestamps();
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
