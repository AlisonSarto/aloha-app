<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionLedger extends Model
{
    protected $table = 'commission_ledger';

    protected $fillable = [
        'seller_id', 'store_id', 'gestao_click_order_id',
        'order_date', 'sale_value', 'packages_count',
        'commission_type', 'commission_rate', 'commission_value',
        'status', 'paid_at', 'notes', 'canceled_by', 'cancel_reason',
    ];

    protected $casts = [
        'order_date'       => 'date',
        'sale_value'       => 'decimal:2',
        'commission_rate'  => 'decimal:2',
        'commission_value' => 'decimal:2',
        'packages_count'   => 'integer',
        'paid_at'          => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

}
