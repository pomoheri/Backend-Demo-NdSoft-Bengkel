<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceLabour extends Model
{
    protected $table = 'service_labour';
    protected $fillable = [
        'transaction_unique',
        'labour_id',
        'frt',
        'discount',
        'subtotal',
    ];

    public function labour()
    {
        return $this->belongsTo(Labour::class, 'labour_id', 'id');
    }

}
