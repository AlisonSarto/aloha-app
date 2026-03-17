<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreHour extends Model
{
    protected $fillable = [
        'store_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_open'
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
