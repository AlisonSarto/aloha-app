<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'enabled_modules',
    ];

    protected $casts = [
        'enabled_modules' => 'array',
    ];
}
