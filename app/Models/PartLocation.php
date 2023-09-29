<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartLocation extends Model
{
    protected $table = 'part_location';
    protected $fillable = [
        'code',
        'location'
    ];
}
