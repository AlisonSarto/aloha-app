<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DeliveryConfig;

class OrderController extends Controller
{
    /**
     * Returns mock flavors — will be replaced by GestaoClick service integration.
     */
    private function getMockFlavors(): array
    {
        return [
            ['id' => 1, 'name' => 'Coco',               'color' => '#f1f1f1', 'emoji' => '🥥'],
            ['id' => 2, 'name' => 'Morango',            'color' => '#e91e63', 'emoji' => '🍓'],
            ['id' => 3, 'name' => 'Maracujá',           'color' => '#ff9800', 'emoji' => '🥭'],
            ['id' => 4, 'name' => 'Melancia',           'color' => '#43a047', 'emoji' => '🍉'],
            ['id' => 5, 'name' => 'Maçã Verde',         'color' => '#7cb342', 'emoji' => '🍏'],
            ['id' => 6, 'name' => 'Pêssego com Morango','color' => '#ff7043', 'emoji' => '🍑'],
            ['id' => 7, 'name' => 'Laranja',            'color' => '#fb8c00', 'emoji' => '🍊'],
            ['id' => 8, 'name' => 'Limão',              'color' => '#c0ca33', 'emoji' => '🍋'],
            ['id' => 9, 'name' => 'Pitaya',             'color' => '#d81b60', 'emoji' => '🐉'],
        ];
    }

    public function index()
    {
        return view('client.orders.index');
    }

    public function create()
    {
        $store          = activeStore();
        $flavors        = $this->getMockFlavors();
        $priceRangesData = $store->priceTable->ranges()
            ->orderBy('min_quantity')
            ->get()
            ->map(fn($r) => [
                'min_quantity' => $r->min_quantity,
                'max_quantity' => $r->max_quantity,
                'unit_price'   => (float) $r->unit_price * 28,
            ])
            ->values()
            ->toArray();
        $deliveryConfig = DeliveryConfig::current();

        return view('client.orders.create', compact('flavors', 'priceRangesData', 'store', 'deliveryConfig'));
    }
}
