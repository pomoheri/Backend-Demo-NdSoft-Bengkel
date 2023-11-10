<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceInvoice extends Model
{
    protected $table = 'service_invoice';
    protected $fillable = [
        'transaction_code',
        'transaction_unique',
        'payment_method',
        'payment_gateway',
        'status',
        'created_by',
        'closed_by',
        'closed_at',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'transaction_unique', 'transaction_unique');
    }
}
