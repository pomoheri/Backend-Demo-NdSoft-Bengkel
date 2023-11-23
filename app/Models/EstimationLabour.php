<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimationLabour extends Model
{
   protected $table = 'estimation_labour';
   protected $fillable = [
        'estimation_unique',
        'labour_id',
        'frt',
        'subtotal',
        'discount'
   ];
}
