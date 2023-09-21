<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';
    protected $fillable = [
        'car_type_id',
        'customer_id',
        'lisence_plat',
        'engine_no',
        'color',
        'year',
        'las_km',
        'trnasmission',
        'created_by',
        'updated_by'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'id');
    }

    public function carType()
    {
        return $this->belongsTo(Cartype::class, 'car_type_id', 'id');
    }

}
