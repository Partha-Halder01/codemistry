<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_uid',
        'user_id',
        'service_id',
        'payment_type',
        'total_service_price',
        'amount_paid',
        'razorpay_payment_id',
        'coupon_code',
        'status',
        'order_date',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
