<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_request';
    protected $fillable = [
        'transaction_unique',
        'request',
        'solution',
    ];
}
