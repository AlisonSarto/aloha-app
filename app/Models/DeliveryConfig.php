<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryConfig extends Model
{
    protected $fillable = ['delivery_days', 'lead_days', 'late_direction'];

    protected $casts = [
        'delivery_days' => 'array',
        'lead_days'     => 'integer',
    ];

    /**
     * Returns the single delivery config row, creating it with defaults if absent.
     */
    public static function current(): self
    {
        return static::first() ?? static::create([
            'delivery_days' => [0, 1, 2, 3, 4, 5], // Mon–Sat
            'lead_days'     => 1,
            'late_direction' => 'after',
        ]);
    }
}
