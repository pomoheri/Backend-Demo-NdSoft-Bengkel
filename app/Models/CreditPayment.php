<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $table = 'credit_payment';
    protected $fillable = [
        'transaction_unique',
        'date',
        'total',
        'amount',
        'balance',
        'created_by',
        'remark',
        'deleted_by',
    ];
}
