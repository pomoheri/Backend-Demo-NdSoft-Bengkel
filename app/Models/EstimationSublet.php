<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimationSublet extends Model
{
    protected $table = 'estimation_sublet';
    protected $fillable = [
        'estimation_unique',
        'sublet',
        'total'
    ];
}
