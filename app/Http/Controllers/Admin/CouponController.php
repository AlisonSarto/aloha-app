<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $coupons = Coupon::when($search, fn($q) => $q->where('code', 'like', '%' . $search . '%'))
            ->when($status !== null && $status !== '', fn($q) => $q->where('is_active', $status === '1'))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons', 'search', 'status'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code',
            'discount_type'   => 'required|in:percent,fixed',
            'discount_value'  => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'usage_per_user'  => 'nullable|integer|min:1',
            'starts_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        Coupon::create([
            'code'            => strtoupper($request->code),
            'discount_type'   => $request->discount_type,
            'discount_value'  => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount'    => $request->max_discount,
            'usage_limit'     => $request->usage_limit,
            'usage_per_user'  => $request->usage_per_user,
            'starts_at'       => $request->starts_at,
            'expires_at'      => $request->expires_at,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Cupom criado com sucesso.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'discount_type'   => 'required|in:percent,fixed',
            'discount_value'  => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'usage_limit'     => 'nullable|integer|min:1',
            'usage_per_user'  => 'nullable|integer|min:1',
            'starts_at'       => 'nullable|date',
            'expires_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);

        $coupon->update([
            'discount_type'   => $request->discount_type,
            'discount_value'  => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount'    => $request->max_discount,
            'usage_limit'     => $request->usage_limit,
            'usage_per_user'  => $request->usage_per_user,
            'starts_at'       => $request->starts_at,
            'expires_at'      => $request->expires_at,
            'is_active'       => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Cupom atualizado com sucesso.');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $msg = $coupon->is_active ? 'Cupom ativado com sucesso.' : 'Cupom desativado com sucesso.';

        return redirect()->route('admin.coupons.index')->with('success', $msg);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Cupom excluído com sucesso.');
    }
}
