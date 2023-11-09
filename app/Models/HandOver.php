<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class HandOver extends Model
{
    protected $table = 'hand_over';
    protected $fillable = [
        'hand_over_unique',
        'vehicle_id',
        'status',
        'carrier',
        'carrier_phone',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->hand_over_unique = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function handOverRequest()
    {
        return $this->hasMany(HandOverRequest::class, 'hand_over_unique', 'hand_over_unique');
    }
}
