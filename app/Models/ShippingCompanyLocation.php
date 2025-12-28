<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompanyLocation extends Model
{
  protected $table = 'shipping_companies_locations'; // <-- هنا
  protected $fillable = ['name', 'lat', 'lang', 'phone', 'address'];

  protected $casts = [
    'lat' => 'decimal:7',
    'lang' => 'decimal:7',
  ];
}
