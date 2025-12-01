<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompanyLocation extends Model
{
    protected $fillable = ['name', 'lat', 'lang'];

    protected $casts = [
        'lat' => 'decimal:7',
        'lang' => 'decimal:7',
    ];
}
