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
        'km',
        'created_by',
        'updated_by'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function serviceRequest()
    {
        return $this->hasMany(ServiceRequest::class, 'transaction_unique', 'transaction_unique');
    }

    public function serviceSublet()
    {
        return $this->hasMany(ServiceSublet::class, 'transaction_unique', 'transaction_unique');
    }

    public function serviceLabour()
    {
        return $this->hasMany(ServiceLabour::class, 'transaction_unique', 'transaction_unique');
    }

    public function sellSparepartDetail()
    {
        return $this->hasMany(SellSparepartDetail::class, 'transaction_unique', 'transaction_unique');
    }

    public function serviceInvoice()
    {
        return $this->hasOne(ServiceInvoice::class, 'transaction_unique', 'transaction_unique');
    }
}
