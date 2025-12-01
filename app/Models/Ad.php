<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['name', 'image', 'desc', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
