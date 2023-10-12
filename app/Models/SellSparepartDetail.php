<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellSparepartDetail extends Model
{
    protected $table = 'sell_spare_part_detail';
    protected $fillable = [
        'transaction_unique',
        'spare_part_id',
        'quantity',
        'discount',
        'subtotal'
    ];
}
