<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name'
    ];

    public function adAccounts () {
        return $this->hasMany('App\Models\AdAccount', 'product_id');
    }
}
