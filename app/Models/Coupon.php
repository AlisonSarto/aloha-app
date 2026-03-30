<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'usage_limit',
        'usage_per_user',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value'  => 'float',
        'min_order_value' => 'float',
        'max_discount'    => 'float',
        'is_active'       => 'boolean',
        'starts_at'       => 'datetime',
        'expires_at'      => 'datetime',
    ];
}
