<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\DeliveryConfig;
use App\Services\GestaoClickService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private GestaoClickService $gestaoClick) {}

    public function index(Request $request)
    {
        $store  = activeStore();
        $page   = max(1, (int) $request->get('page', 1));

        $result = $this->gestaoClick->getOrders($store->gestao_click_id, $page);

        $orders      = $result['data'];
        $meta        = $result['meta'];
        $currentPage = $meta['pagina_atual']    ?? $page;
        $nextPage    = $meta['proxima_pagina']  ?? null;
        $prevPage    = $currentPage > 1 ? $currentPage - 1 : null;

        return view('client.orders.index', compact('orders', 'currentPage', 'nextPage', 'prevPage'));
    }

    public function show(string $id)
    {
        $response = $this->gestaoClick->getOrder($id);
        $order    = $response['data'];

        return view('client.orders.show', compact('order'));
    }

    public function create()
    {
        $store  = activeStore();
        $result = $this->gestaoClick->getProducts($store->gestao_click_id);

        $flavors = collect($result)
            ->map(fn($p) => [
                'id'    => $p['id'],
                'name'  => $p['nome'],
                'color' => $this->flavorColor($p['nome']),
                'emoji' => $this->flavorEmoji($p['nome']),
            ])
            ->values()
            ->toArray();

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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'flavors'       => 'required|array|min:1',
            'flavors.*.id'  => 'required|string',
            'flavors.*.qty' => 'required|integer|min:1',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_date' => 'required|date|after_or_equal:today',
            'payment'       => 'required|in:pix,boleto,cash,card',
            'notes'         => 'nullable|string|max:500',
        ]);

        $store = activeStore();

        $result = $this->gestaoClick->createOrder($store->gestao_click_id, [
            'data_entrega'    => $validated['delivery_date'],
            'forma_pagamento' => $validated['payment'],
            'tipo_entrega'    => $validated['delivery_type'] === 'delivery' ? 'entrega' : 'retirada',
            'observacao'      => $validated['notes'] ?? '',
            'itens'           => collect($validated['flavors'])->map(fn($f) => [
                'produto_id' => $f['id'],
                'quantidade' => $f['qty'],
            ])->toArray(),
        ]);

        return response()->json([
            'success'  => true,
            'order_id' => $result['data']['id'],
            'numero'   => $result['data']['numero'],
            'result'   => $result
        ]);
    }

    // ── Flavor appearance helpers ──────────────────────────────────────────────

    private function flavorColor(string $name): string
    {
        $n = mb_strtolower($name);

        return match (true) {
            str_contains($n, 'coco')     => '#f1f1f1',
            str_contains($n, 'morango')  => '#e91e63',
            str_contains($n, 'maracuj')  => '#ff9800',
            str_contains($n, 'melancia') => '#43a047',
            str_contains($n, 'maç')      => '#7cb342',
            str_contains($n, 'ssego')    => '#ff7043',
            str_contains($n, 'laranja')  => '#fb8c00',
            str_contains($n, 'lim')      => '#c0ca33',
            str_contains($n, 'pitaya')   => '#d81b60',
            default                      => '#a5b4fc',
        };
    }

    private function flavorEmoji(string $name): string
    {
        $n = mb_strtolower($name);

        return match (true) {
            str_contains($n, 'coco')     => '🥥',
            str_contains($n, 'morango')  => '🍓',
            str_contains($n, 'maracuj')  => '🥭',
            str_contains($n, 'melancia') => '🍉',
            str_contains($n, 'maç')      => '🍏',
            str_contains($n, 'ssego')    => '🍑',
            str_contains($n, 'laranja')  => '🍊',
            str_contains($n, 'lim')      => '🍋',
            str_contains($n, 'pitaya')   => '🐉',
            default                      => '🍨',
        };
    }
}
