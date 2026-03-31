<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\DeliveryConfig;
use App\Services\GestaoClickService;
use App\Services\BotconversaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class OrderController extends Controller
{
    public function __construct(
        private GestaoClickService $gestaoClick,
        private BotconversaService $botconversa,
    ) {}

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
            'coupon_code'   => 'nullable|string|max:50',
            'subtotal'      => 'nullable|numeric|min:0',
            'shipping'      => 'nullable|numeric|min:0',
        ]);

        $store = activeStore();

        // ── Coupon handling ────────────────────────────────────────────────────
        $couponData = null;

        if (! empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($validated['coupon_code']))->first();

            if ($coupon) {
                $subtotal = (float) ($validated['subtotal'] ?? 0);
                $shipping = (float) ($validated['shipping'] ?? (float) $store->shipping_amount);
                $error    = $coupon->validate($store, $subtotal, $validated['delivery_type'], auth()->id());

                if (! $error) {
                    $discount = $coupon->calculateDiscount($subtotal, $shipping);
                    $discount_value = (float) $coupon->discount_value;

                    $couponData = [
                        'coupon'         => $coupon,
                        'discount'       => $discount,
                        'discount_value' => $discount_value,
                        'subtotal'       => $subtotal,
                        'shipping'       => $shipping,
                        'description'    => $this->couponObservation($coupon, $subtotal, $shipping, $discount),
                    ];
                }
            }
        }

        $result = $this->gestaoClick->createOrder($store->gestao_click_id, [
            'data_entrega'    => $validated['delivery_date'],
            'forma_pagamento' => $validated['payment'],
            'tipo_entrega'    => $validated['delivery_type'] === 'delivery' ? 'entrega' : 'retirada',
            'observacao'      => $validated['notes'] ?? '',
            'itens'           => collect($validated['flavors'])->map(fn($f) => [
                'produto_id' => $f['id'],
                'quantidade' => $f['qty'],
            ])->toArray(),
            'coupon_discount'       => $couponData ? $couponData['discount'] : 0.0,
            'coupon_discount_value' => $couponData ? $couponData['discount_value'] : 0.0,
            'coupon_type'           => $couponData ? $couponData['coupon']->discount_type : null,
            'coupon_observation'    => $couponData ? $couponData['description'] : null,
        ]);

        // ── Record coupon usage ────────────────────────────────────────────────
        if ($couponData) {
            CouponUsage::create([
                'coupon_id'              => $couponData['coupon']->id,
                'user_id'                => auth()->id(),
                'store_id'               => $store->id,
                'gestao_click_order_id'  => $result['data']['id'] ?? null,
            ]);
        }

        $botconversaResult = null;
        $phone = (string) (auth()->user()?->client?->phone ?? '');
        $saleId = (string) ($result['data']['id'] ?? '');
        $saleCode = (string) ($result['data']['numero'] ?? $result['data']['codigo'] ?? $saleId);

        if ($phone !== '' && $saleId !== '' && $saleCode !== '') {
            try {
                $botconversaResult = $this->botconversa->newOrderNotification(
                    $phone,
                    $saleId,
                    $saleCode,
                    (string) $store->name,
                );
            } catch (Throwable $e) {
                // Do not fail order creation if notification webhook is unavailable.
                $botconversaResult = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }



        return response()->json([
            'success'  => true,
            'order_id' => $result['data']['id'],
            'numero'   => $result['data']['numero'],
            'result'   => $result,
            'botconversa' => $botconversaResult,
        ]);
    }

    private function couponObservation(Coupon $coupon, float $subtotal, float $shipping, float $discount): string
    {
        $typeLabel = match ($coupon->discount_type) {
            'percent'  => $coupon->discount_value . '% de desconto sobre R$ ' . number_format($subtotal, 2, ',', '.'),
            'fixed'    => 'R$ ' . number_format($coupon->discount_value, 2, ',', '.') . ' de desconto fixo',
            'shipping' => 'Frete grátis (frete de R$ ' . number_format($shipping, 2, ',', '.') . ')',
            default    => $coupon->discount_type,
        };

        return 'Cupom: ' . $coupon->code . ' (' . $typeLabel . ') — R$ ' . number_format($discount, 2, ',', '.');
    }

    // ── Flavor appearance helpers ──────────────────────────────────────────────

    private function flavorColor(string $name): string
    {
        $n = mb_strtolower($name);

        return match (true) {
            str_contains($n, 'coco')     => '#f1f1f1',
            str_contains($n, 'ssego')    => '#ff7043',
            str_contains($n, 'morango')  => '#e91e63',
            str_contains($n, 'maracuj')  => '#ff9800',
            str_contains($n, 'melancia') => '#43a047',
            str_contains($n, 'maç')      => '#7cb342',
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
            str_contains($n, 'ssego')    => '🍑',
            str_contains($n, 'morango')  => '🍓',
            str_contains($n, 'maracuj')  => '🥭',
            str_contains($n, 'melancia') => '🍉',
            str_contains($n, 'maç')      => '🍏',
            str_contains($n, 'laranja')  => '🍊',
            str_contains($n, 'lim')      => '🍋',
            str_contains($n, 'pitaya')   => '🐉',
            default                      => '🍨',
        };
    }
}
