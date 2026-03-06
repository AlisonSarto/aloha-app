<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTableRange extends Model
{
    protected $fillable = [
        'price_table_id',
        'min_quantity',
        'max_quantity',
        'unit_price'
    ];

    public function priceTable()
    {
        return $this->belongsTo(PriceTable::class);
    }

}
