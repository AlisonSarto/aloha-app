<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\SellerMetricsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function commissions(Request $request, SellerMetricsService $metrics)
    {
        $seller = auth()->user()->seller;

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->endOfMonth()->toDateString()))->endOfDay();

        $storeId = $request->input('store_id');

        $report = $metrics->commissionReport($seller, $from, $to, $storeId ? (int) $storeId : null);

        $stores = Store::where('seller_id', $seller->id)
            ->where('seller_assignment_status', 'approved')
            ->orderBy('name')
            ->get();

        return view('seller.reports.commissions', compact('seller', 'report', 'stores', 'from', 'to', 'storeId'));
    }

    public function stores(Request $request, SellerMetricsService $metrics)
    {
        $seller = auth()->user()->seller;

        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->input('to',   now()->endOfMonth()->toDateString()))->endOfDay();

        $report = $metrics->storeReport($seller, $from, $to);

        return view('seller.reports.stores', compact('seller', 'report', 'from', 'to'));
    }

    public function goals(Request $request, SellerMetricsService $metrics)
    {
        $seller = auth()->user()->seller;
        $year   = (int) $request->input('year',  now()->year);
        $month  = (int) $request->input('month', now()->month);

        $from = Carbon::create($year, $month, 1)->startOfDay();
        $to   = $from->copy()->endOfMonth()->endOfDay();

        $kpis       = $metrics->dashboardKpis($seller, $from, $to);
        $goal       = $seller->goalForMonth($year, $month);
        $progress   = $kpis['goal_progress'];

        return view('seller.reports.goals', compact('seller', 'goal', 'kpis', 'progress', 'year', 'month', 'from', 'to'));
    }
}
