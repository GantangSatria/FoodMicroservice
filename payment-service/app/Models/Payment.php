<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model {
    protected $table = 'payments';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'order_id', 'amount', 'status', 'midtrans_token', 'midtrans_redirect_url'
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];
}
