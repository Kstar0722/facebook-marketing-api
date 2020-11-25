<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbAccount extends Model
{
    protected $fillable = [
        'user_id',
        'fb_user_id',
        'fb_access_token',
        'fb_token_expiration_time',
        'first_name',
        'last_name',
        'profile_pic',
        'locale',
        'timezone',
        'gender'
    ];
}
