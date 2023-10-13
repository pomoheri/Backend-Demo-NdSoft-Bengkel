<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    protected $table = 'purchase_order_detail';
    protected $fillable = [
        'transaction_unique',
        'spare_part_id',
        'quantity',
        'subtotal',
        'perpiece',
        'status'
    ];
    public function sparepart(){
        return $this->belongsTo(SparePart::class, 'spare_part_id', 'id');
    }

    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class, 'transaction_unique', 'transaction_unique');
    }
}
