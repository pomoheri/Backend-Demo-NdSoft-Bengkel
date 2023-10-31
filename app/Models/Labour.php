<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Labour extends Model
{
    protected $table = 'labour';
    protected $fillable = [
        'labour_code',
        'frt',
        'price',
        'created_by',
        'updated_by',
    ];
}
