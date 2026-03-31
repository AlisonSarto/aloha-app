<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_public',
    ];

    protected $casts = [
        'discount_value'  => 'float',
        'min_order_value' => 'float',
        'max_discount'    => 'float',
        'is_active'       => 'boolean',
        'is_public'       => 'boolean',
        'starts_at'       => 'datetime',
        'expires_at'      => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'coupon_store')
                    ->withPivot('usage_limit')
                    ->withTimestamps();
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ── Validation ─────────────────────────────────────────────────────────────

    /**
     * Validate coupon for a given store, subtotal, delivery type, and user.
     * Returns null on success or a human-readable error message.
     */
    public function validate(Store $store, float $subtotal, string $deliveryType, int $userId): ?string
    {
        if (! $this->is_active) {
            return 'Cupom inativo.';
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'Cupom ainda não está válido.';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'Cupom expirado.';
        }

        // Shipping coupon unavailable for pickup orders
        if ($this->discount_type === 'shipping' && $deliveryType === 'pickup') {
            return 'Este cupom de frete não está disponível para pedidos com retirada.';
        }

        if ($this->min_order_value && $subtotal < $this->min_order_value) {
            return 'Pedido mínimo de R$ ' . number_format($this->min_order_value, 2, ',', '.') . ' não atingido.';
        }

        // Store-specific restriction: if stores are linked, only those stores can use it
        $storeIds = $this->stores()->pluck('stores.id');
        if ($storeIds->isNotEmpty() && ! $storeIds->contains($store->id)) {
            return 'Cupom indisponível para esta loja.';
        }

        // Global usage limit
        if ($this->usage_limit !== null && $this->usages()->count() >= $this->usage_limit) {
            return 'Limite de uso global do cupom atingido.';
        }

        // Per-store usage limit (from pivot)
        $pivot = $this->stores()->where('stores.id', $store->id)->first()?->pivot;
        if ($pivot && $pivot->usage_limit !== null) {
            $storeCount = $this->usages()->where('store_id', $store->id)->count();
            if ($storeCount >= $pivot->usage_limit) {
                return 'Limite de uso do cupom para esta loja atingido.';
            }
        }

        // Per-user usage limit
        if ($this->usage_per_user !== null) {
            $userCount = $this->usages()->where('user_id', $userId)->count();
            if ($userCount >= $this->usage_per_user) {
                return 'Você já atingiu o limite de uso deste cupom.';
            }
        }

        return null;
    }

    /**
     * Calculate the discount amount given current subtotal and shipping.
     */
    public function calculateDiscount(float $subtotal, float $shipping): float
    {
        $discount = match ($this->discount_type) {
            'percent'  => $subtotal * ($this->discount_value / 100),
            'fixed'    => $this->discount_value,
            'shipping' => $shipping,
            default    => 0.0,
        };

        if ($this->max_discount !== null && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        return round($discount, 2);
    }
}
