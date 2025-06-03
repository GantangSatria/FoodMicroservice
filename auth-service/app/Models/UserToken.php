<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    public $timestamps = false;
    protected $table = 'user_tokens';
    protected $fillable = [
        'user_uuid', 'jwt_id', 'issued_at', 'expired_at', 'is_revoked'
    ];
}
