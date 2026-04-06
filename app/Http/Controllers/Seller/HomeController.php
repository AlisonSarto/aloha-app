<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\SellerMetricsService;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(SellerMetricsService $metrics)
    {
        $seller = auth()->user()->seller;

        if (!$seller) {
            abort(403, 'Perfil de vendedor não encontrado.');
        }

        $from = now()->startOfMonth();
        $to   = now()->endOfMonth();
        $kpis = $metrics->dashboardKpis($seller, $from, $to);

        $recentStores = $seller->stores()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingClaims = $seller->storeClaims()
            ->where('status', 'pending')
            ->with('store')
            ->limit(5)
            ->get();

        return view('seller.home', compact('seller', 'kpis', 'recentStores', 'pendingClaims'));
    }
}
