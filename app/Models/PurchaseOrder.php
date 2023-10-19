<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $table = 'purchase_order';
    protected $fillable = [
        'id',
        'transaction_code',
        'transaction_unique',
        'supplier_id',
        'invoice_number',
        'invoice_date',
        'total',
        'status',
        'remark',
        'created_by',
        'payment_method',
        'payment_due_date',
        'is_paid'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->transaction_unique = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'transaction_unique', 'transaction_unique');
    }
}
