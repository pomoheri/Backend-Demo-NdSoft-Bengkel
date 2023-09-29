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
        'created_by'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->transaction_code = Uuid::uuid4()->toString();
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
}
