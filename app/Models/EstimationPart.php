<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimationPart extends Model
{
    protected $table = 'estimation_part';
    protected $fillable = [
        'estimation_unique',
        'sparepart_id',
        'quantity',
        'subtotal',
        'discount',
        'total',
        'profit'
    ];
}
