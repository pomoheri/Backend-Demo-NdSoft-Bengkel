<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    protected $table = 'spare_part';
    protected $fillable = [
        'part_number',
        'name',
        'car_brand_id',
        'is_genuine',
        'category',
        'stock',
        'buying_price',
        'selling_price',
        'profit',
        'location_id',
        'created_by',
        'updated_by'
    ];
    public function partLocations()
    {
        return $this->belongsTo(PartLocation::class, 'location_id', 'id');
    }

    public function carBrands()
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id', 'id');
    }
}
