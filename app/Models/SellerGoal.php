<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerGoal extends Model
{
    protected $fillable = [
        'seller_id', 'year', 'month',
        'new_stores_target', 'active_stores_target', 'packages_target',
        'new_stores_enabled', 'active_stores_enabled', 'packages_enabled',
    ];

    protected $casts = [
        'year'                  => 'integer',
        'month'                 => 'integer',
        'new_stores_target'     => 'integer',
        'active_stores_target'  => 'integer',
        'packages_target'       => 'integer',
        'new_stores_enabled'    => 'boolean',
        'active_stores_enabled' => 'boolean',
        'packages_enabled'      => 'boolean',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public static function forSellerMonth(int $sellerId, int $year, int $month): self
    {
        return static::firstOrCreate(
            ['seller_id' => $sellerId, 'year' => $year, 'month' => $month],
            [
                'new_stores_enabled'    => false,
                'active_stores_enabled' => false,
                'packages_enabled'      => false,
            ]
        );
    }
}
