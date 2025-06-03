<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'uuid', 'order_number', 'user_uuid', 'restaurant_id',
        'total_amount', 'status', 'payment_status', 'delivery_address'
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'total_amount' => 'float'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
            $model->order_number = 'ORD-' . strtoupper(Str::random(8));
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
