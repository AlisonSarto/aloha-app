<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'commission_new_client',
        'commission_recurring',
    ];

    protected $casts = [
        'commission_new_client' => 'float',
        'commission_recurring'  => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    /** Stores approved for commission reporting */
    public function approvedStores(): HasMany
    {
        return $this->hasMany(Store::class)->where('seller_assignment_status', 'approved');
    }

    public function storeClaims(): HasMany
    {
        return $this->hasMany(SellerStoreClaim::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(CommissionLedger::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(SellerGoal::class);
    }

    public function goalForMonth(int $year, int $month): SellerGoal
    {
        return SellerGoal::forSellerMonth($this->id, $year, $month);
    }

    /** Orders up to (and including) this count are considered "new_store" commission type */
    public const NEW_CLIENT_ORDERS = 3;
}
