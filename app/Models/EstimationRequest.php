<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimationRequest extends Model
{
    protected $table = 'estimation_request';
    protected $fillable = [
        'estimation_unique',
        'request'
    ];
}
