<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthUser extends Model
{
    protected $table = 'auth_users';
    protected $fillable = [
        'uuid', 'email', 'password', 'status',
        'email_verified_at', 'last_login_at', 'locked_until'
    ];
}
