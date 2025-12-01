<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductKey extends Model
{
    protected $fillable = ['key_name'];

    public function values()
    {
        return $this->hasMany(ProductValue::class);
    }
}
