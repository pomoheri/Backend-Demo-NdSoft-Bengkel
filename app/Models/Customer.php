<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'created_by',
        'updated_by'
    ];
    public function vehicle()
    {
        return $this->hasMany(Vehicle::class);
    }
}
