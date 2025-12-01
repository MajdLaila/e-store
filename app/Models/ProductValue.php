<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductValue extends Model
{
    protected $fillable = ['product_key_id', 'product_value'];

    public function key()
    {
        return $this->belongsTo(ProductKey::class, 'product_key_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_value_product', 'product_value_id', 'product_id');
    }
}
