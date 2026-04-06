<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gestao_click_id',
        'cnpj',
        'legal_name',
        'shipping_amount',
        'price_table_id',
        'can_use_boleto',
        'boleto_due_days',
        'address_cep',
        'address_street',
        'address_number',
        'address_complement',
        'address_district',
        'address_city',
        'address_state',
        'seller_id',
        'seller_assignment_status',
        'seller_assignment_approved_by',
        'seller_assignment_approved_at',
        'seller_assignment_reason',
        'seller_assignment_requested_by',
        'seller_assignment_requested_at',
    ];

    protected $casts = [
        'shipping_amount'                 => 'decimal:2',
        'orders_count'                    => 'integer',
        'can_use_boleto'                  => 'boolean',
        'boleto_due_days'                 => 'integer',
        'seller_assignment_approved_at'   => 'datetime',
        'seller_assignment_requested_at'  => 'datetime',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)
                    ->withTimestamps();
    }

    public function priceTable(): BelongsTo
    {
        return $this->belongsTo(PriceTable::class)
                    ->withDefault(fn () => PriceTable::default());
    }

    public function storeHours(): HasMany
    {
        return $this->hasMany(StoreHour::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_store')
                    ->withPivot('usage_limit')
                    ->withTimestamps();
    }

    public function seller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function sellerClaims(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SellerStoreClaim::class);
    }

    public function commissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CommissionLedger::class);
    }

    public function isSellerApproved(): bool
    {
        return $this->seller_assignment_status === 'approved' && $this->seller_id !== null;
    }

    public function hasLinkedClients(): bool
    {
        return $this->clients()->exists();
    }

}
