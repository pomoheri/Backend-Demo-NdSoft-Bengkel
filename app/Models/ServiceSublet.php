<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSublet extends Model
{
    protected $table = 'service_sublet';
    protected $fillable = [
        'transaction_unique',
        'sublet',
        'subtotal',
    ];
}
