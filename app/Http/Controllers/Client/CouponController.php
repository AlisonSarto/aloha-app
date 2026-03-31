<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * List public coupons available for the active store, with per-coupon validity check.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'subtotal'      => 'nullable|numeric|min:0',
            'shipping'      => 'nullable|numeric|min:0',
            'delivery_type' => 'nullable|in:delivery,pickup',
        ]);

        $store        = activeStore();
        $subtotal     = (float) ($request->subtotal ?? 0);
        $shipping     = (float) ($request->shipping ?? (float) $store->shipping_amount);
        $deliveryType = $request->delivery_type ?? 'delivery';
        $userId       = auth()->id();

        $coupons = Coupon::where('is_active', true)
            ->where('is_public', true)
            ->get()
            ->filter(function (Coupon $coupon) use ($store) {
                $storeIds = $coupon->stores()->pluck('stores.id');

                return $storeIds->isEmpty() || $storeIds->contains($store->id);
            })
            ->map(function (Coupon $c) use ($store, $subtotal, $shipping, $deliveryType, $userId) {
                $error = $c->validate($store, $subtotal, $deliveryType, $userId);

                return array_merge($this->formatCoupon($c), [
                    'available'          => $error === null,
                    'unavailable_reason' => $error,
                ]);
            })
            ->sortByDesc('available')
            ->values();

        return response()->json(['coupons' => $coupons]);
    }

    /**
     * Validate a coupon code for the active store and return discount info.
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code'          => 'required|string|max:50',
            'subtotal'      => 'required|numeric|min:0',
            'shipping'      => 'required|numeric|min:0',
            'delivery_type' => 'required|in:delivery,pickup',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (! $coupon) {
            return response()->json(['valid' => false, 'message' => 'Cupom não encontrado.'], 422);
        }

        $store  = activeStore();
        $error  = $coupon->validate($store, (float) $request->subtotal, $request->delivery_type, auth()->id());

        if ($error) {
            return response()->json(['valid' => false, 'message' => $error], 422);
        }

        $discount = $coupon->calculateDiscount((float) $request->subtotal, (float) $request->shipping);

        return response()->json([
            'valid'   => true,
            'coupon'  => array_merge($this->formatCoupon($coupon), [
                'calculated_discount' => $discount,
            ]),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function formatCoupon(Coupon $c): array
    {
        return [
            'code'          => $c->code,
            'discount_type' => $c->discount_type,
            'discount_value' => $c->discount_value,
            'min_order_value' => $c->min_order_value,
            'max_discount'  => $c->max_discount,
            'expires_at'    => $c->expires_at?->format('d/m/Y'),
            'label'         => $this->typeLabel($c),
            'description'   => $this->typeDescription($c),
        ];
    }

    private function typeLabel(Coupon $c): string
    {
        return match ($c->discount_type) {
            'percent'  => number_format($c->discount_value, 0, ',', '.') . '% de desconto',
            'fixed'    => 'R$ ' . number_format($c->discount_value, 2, ',', '.') . ' de desconto',
            'shipping' => 'Frete grátis',
            default    => $c->code,
        };
    }

    private function typeDescription(Coupon $c): string
    {
        $parts = [];

        if ($c->discount_type === 'shipping') {
            $parts[] = 'Isenção total do frete';
        } elseif ($c->discount_type === 'percent') {
            $parts[] = $c->discount_value . '% de desconto sobre o subtotal';
            if ($c->max_discount) {
                $parts[] = 'máx. R$ ' . number_format($c->max_discount, 2, ',', '.');
            }
        } else {
            $parts[] = 'R$ ' . number_format($c->discount_value, 2, ',', '.') . ' de desconto fixo';
        }

        if ($c->min_order_value) {
            $parts[] = 'pedido mínimo R$ ' . number_format($c->min_order_value, 2, ',', '.');
        }

        return implode(' — ', $parts);
    }
}
