<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gestao_click_id',
        'shipping_amount',
        'price_table_id',
        'can_use_boleto',
        'boleto_due_days',
    ];

    protected $casts = [
        'shipping_amount' => 'decimal:2',
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
