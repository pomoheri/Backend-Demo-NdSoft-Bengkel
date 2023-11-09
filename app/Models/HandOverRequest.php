<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandOverRequest extends Model
{
    protected $table = 'hand_over_request';
    protected $fillable = [
        'hand_over_unique',
        'request'
    ];

}
