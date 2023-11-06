<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Estimation extends Model
{
    protected $table = 'estimation';
    protected $fillable = [
        'estimation_unique',
        'vehicle_id',
        'carrier',
        'carrier_phone',
        'labour',
        'spare_part',
        'sublet',
        'total',
        'remark',
        'status',
        'created_by',
        'updated_by'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->estimation_unique = Uuid::uuid4()->toString();
            } catch (UnsatisfiedDependencyException $e) {
                abort(500, $e->getMessage());
            }
        });
    }

    public function Vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

    public function estimationRequest()
    {
        return $this->hasMany(EstimationRequest::class, 'estimation_unique', 'estimation_unique');
    }
}
