<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'menu_item_id', 'menu_item_snapshot', 'quantity', 'total_price'
    ];

    protected $casts = [
        'menu_item_snapshot' => 'array',
        'total_price' => 'float'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
