<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'full_price',
        'deposit_price',
        'features',
        'cover_image_path',
        'cta_image_path',
        'rating',
        'faq',
        'process_steps',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'faq' => 'array',
        'process_steps' => 'array',
        'is_featured' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
            }
        });

        static::updating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pricings()
    {
        return $this->hasMany(ServicePricing::class);
    }
}
