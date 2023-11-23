<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class SellSparepart extends Model
{
    protected $table = 'sell_spare_part';
    protected $casts = [
        'payment_date' => 'date'
    ];
    protected $fillable = [
        'transaction_code',
        'transaction_unique',
        'name',
        'phone',
        'address',
        'total',
        'payment_date',
        'payment_method',
        'payment_gateway',
        'status',
        'remark',
        'created_by',
        'closed_by',
        'closed_at',
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
    public function details()
    {
        return $this->hasMany(SellSparepartDetail::class, 'transaction_unique', 'transaction_unique');
    }
}
