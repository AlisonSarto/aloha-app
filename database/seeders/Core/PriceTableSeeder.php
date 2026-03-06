<?php

namespace Database\Seeders\Core;

use Illuminate\Database\Seeder;
use App\Models\PriceTable;
use App\Models\PriceTableRange;

class PriceTableSeeder extends Seeder
{
    public function run(): void
    {
        $table = PriceTable::create([
            'name' => 'Tabela Padrão',
            'is_default' => true
        ]);

        $ranges = [
            [
                'min_quantity' => 1,
                'max_quantity' => 29,
                'unit_price' => 1.00
            ],
            [
                'min_quantity' => 30,
                'max_quantity' => 99,
                'unit_price' => 0.90
            ],
            [
                'min_quantity' => 100,
                'max_quantity' => null,
                'unit_price' => 0.80
            ]
        ];

        foreach ($ranges as $range) {
            $table->ranges()->create($range);
        }
    }
}
