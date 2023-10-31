<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $table = 'work_order';
    protected $fillable = [
        'transaction_code',
        'transaction_unique',
        'vehicle_id',
        'total',
        'carrier',
        'carrier_phone',
        'status',
        'remark',
        'technician',
        'created_by',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}
