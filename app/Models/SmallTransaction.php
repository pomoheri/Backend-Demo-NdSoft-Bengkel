<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmallTransaction extends Model
{
    protected $table = 'small_transaction';
    protected $fillable = [
        'date',
        'description',
        'pic',
        'status',
        'category',
        'total',
        'created_by'
    ];
}
