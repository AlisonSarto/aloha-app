<?php

namespace App\Services;

use App\Models\Store;

class PrincingService
{
    public function calculate(Store $store, int $quantity)
    {
        $priceTable = $store->priceTable;

        $range = $priceTable->ranges()
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->first();

        if (!$range) {
            throw new \Exception('Nenhuma faixa de preço encontrada.');
        }

        $unit_price = $range->unit_price;

        return [
            'unit_price' => $unit_price,
            'total_price' => ($unit_price * 28) * $quantity // 28 units for each pack
        ];
    }
}
