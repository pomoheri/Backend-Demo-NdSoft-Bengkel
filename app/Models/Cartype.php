<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartype extends Model
{
    protected $table = 'car_type';
    protected $fillable = [
        'car_brand_id',
        'name',
        'type',
        'cc',
        'engine_type'
    ];

    public function CarBrand()
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id', 'id');
    }
}
