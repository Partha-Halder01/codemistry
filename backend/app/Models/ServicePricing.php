<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePricing extends Model
{
    protected $fillable = [
        'service_id',
        'plan_name',
        'price',
        'end_price',
        'features',
        'is_popular',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
