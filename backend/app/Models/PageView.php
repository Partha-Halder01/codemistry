<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'country',
        'city',
        'path',
        'service_id',
        'time_spent',
        'last_ping_at'
    ];

    protected $casts = [
        'last_ping_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
