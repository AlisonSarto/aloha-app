<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'gestao_click_id',
        'shipping_amount',
        'unit_price',
        'can_use_boleto',
        'boleto_due_days',
    ];

    protected $casts = [
        'shipping_amount' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'orders_count' => 'integer',
        'can_use_boleto' => 'boolean',
        'boleto_due_days' => 'integer',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class)
                    ->withTimestamps();
    }

}
