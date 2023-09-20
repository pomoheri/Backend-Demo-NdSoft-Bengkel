<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
   protected $table = 'supplier';
    protected $fillable = [
        'supplier_code',
        'name',
        'phone',
        'address',
        'pic',
        'created_by',
        'updated_by'
    ];
}
