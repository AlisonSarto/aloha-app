<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'commission_new_client',
        'commission_recurring',
        'monthly_package_target',
    ];

    protected $casts = [
        'commission_new_client' => 'float',
        'commission_recurring' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
