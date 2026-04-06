<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLedger;
use App\Models\Seller;
use App\Models\Store;
use App\Services\CommissionService;
use App\Services\SellerMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request, SellerMetricsService $metrics)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->endOfMonth()->toDateString()))->endOfDay();

        $sellerId = $request->input('seller_id');
        $storeId  = $request->input('store_id');
        $status   = $request->input('status');

        $query = CommissionLedger::with(['seller.user', 'store'])
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('order_date', 'desc');

        if ($sellerId) $query->where('seller_id', $sellerId);
        if ($storeId)  $query->where('store_id', $storeId);
        if ($status)   $query->where('status', $status);

        $ledgers = $query->paginate(30)->withQueryString();

        $sellers = Seller::with('user')->get();
        $stores  = Store::whereNotNull('seller_id')->orderBy('name')->get();

        $globalStats = $metrics->adminGlobalStats($from, $to);

        return view('admin.commissions.index', compact(
            'ledgers', 'sellers', 'stores', 'globalStats',
            'from', 'to', 'sellerId', 'storeId', 'status'
        ));
    }

    public function adjust(Request $request, CommissionLedger $commission, CommissionService $service)
    {
        $request->validate([
            'adjusted_value' => 'required|numeric',
            'reason'         => 'required|string|min:5',
        ]);

        $service->adjustCommission(
            $commission,
            (float) $request->adjusted_value,
            $request->reason,
            auth()->id()
        );

        return back()->with('success', 'Comissão ajustada e registrada na trilha de auditoria.');
    }

    public function standaloneAdjust(Request $request, CommissionService $service)
    {
        $request->validate([
            'seller_id'      => 'required|exists:sellers,id',
            'store_id'       => 'required|exists:stores,id',
            'adjusted_value' => 'required|numeric',
            'reason'         => 'required|string|min:5',
        ]);

        $seller = Seller::findOrFail($request->seller_id);
        $store  = Store::findOrFail($request->store_id);

        $service->standaloneAdjustment(
            $seller,
            (float) $request->adjusted_value,
            $request->reason,
            auth()->id(),
            $store
        );

        return back()->with('success', 'Ajuste manual registrado no ledger.');
    }

    public function destroy(CommissionLedger $commission)
    {
        $commission->delete();

        return back()->with('success', 'Comissão removida.');
    }

    public function markPaid(Request $request, CommissionService $service)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);

        $count = $service->markPaid($request->ids, auth()->id());

        return back()->with('success', "{$count} comissão(ões) marcada(s) como pagas.");
    }

    public function sellerDashboard(Request $request, SellerMetricsService $metrics)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->endOfMonth()->toDateString()))->endOfDay();

        $ranking     = $metrics->sellerPerformanceRanking($from, $to);
        $globalStats = $metrics->adminGlobalStats($from, $to);

        return view('admin.commissions.dashboard', compact('ranking', 'globalStats', 'from', 'to'));
    }
}
