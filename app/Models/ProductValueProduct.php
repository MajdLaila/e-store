<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductValueProduct extends Pivot
{
    protected $table = 'product_value_product';
    protected $fillable = ['product_value_id', 'product_id'];
}
