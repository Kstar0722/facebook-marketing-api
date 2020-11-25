<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdAccount extends Model
{
    protected $fillable = [
        'user_id',
        'fb_user_id',
        'product_id',
        'ad_account_id',
        'ad_account_name'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
