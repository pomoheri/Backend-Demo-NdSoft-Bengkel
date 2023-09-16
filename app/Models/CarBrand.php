<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    protected $table = 'car_brand';
    protected $fillable = [
        'name'
    ];
    
}
