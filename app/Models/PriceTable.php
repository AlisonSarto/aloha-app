<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTable extends Model
{
    protected $fillable = [
        'name',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function ranges()
    {
        return $this->hasMany(PriceTableRange::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public static function default()
    {
        return static::where('is_default', true)->first();
    }
}
