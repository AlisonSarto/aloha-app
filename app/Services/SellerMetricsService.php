<?php

namespace App\Services;

use App\Models\CommissionLedger;
use App\Models\Seller;
use App\Models\SellerGoal;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SellerMetricsService
{
    /**
     * KPIs for the seller dashboard (current month by default).
     */
    public function dashboardKpis(Seller $seller, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $from ??= now()->startOfMonth();
        $to   ??= now()->endOfMonth();

        $approvedStoreIds = $seller->approvedStores()->pluck('id');

        // Commission accumulated in period
        $totalCommission = (float) CommissionLedger::where('seller_id', $seller->id)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->sum('commission_value');

        // New stores registered by seller in period (any status)
        $newStoresCount = Store::where('seller_id', $seller->id)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        // Active stores: approved stores that had at least 1 order in period
        $activeStoresCount = CommissionLedger::where('seller_id', $seller->id)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->distinct('store_id')
            ->count('store_id');

        // Total packages sold through seller's approved stores in period
        $packagesCount = CommissionLedger::where('seller_id', $seller->id)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->sum('packages_count');

        // Total sales value
        $salesValue = CommissionLedger::where('seller_id', $seller->id)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->sum('sale_value');

        // Goal progress
        $goal = $seller->goalForMonth($from->year, $from->month);
        $goalProgress = $this->goalProgress($seller, $goal, $newStoresCount, $activeStoresCount, (int) $packagesCount);

        return [
            'commission_total'    => $totalCommission,
            'new_stores_count'    => $newStoresCount,
            'active_stores_count' => $activeStoresCount,
            'packages_count'      => (int) $packagesCount,
            'sales_value'         => (float) $salesValue,
            'goal'                => $goal,
            'goal_progress'       => $goalProgress,
            'period_from'         => $from,
            'period_to'           => $to,
        ];
    }

    /**
     * Commission report for a seller over a custom period.
     * Returns ledger entries + adjustments + summary grouped optionally by store.
     */
    public function commissionReport(Seller $seller, Carbon $from, Carbon $to, ?int $storeId = null): array
    {
        $query = CommissionLedger::with(['store'])
            ->where('seller_id', $seller->id)
            ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->orderBy('order_date', 'desc');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $entries = $query->get();

        $byStore = $entries->groupBy('store_id')->map(function ($group) {
            return [
                'store'              => $group->first()->store,
                'entries_count'      => $group->count(),
                'total_sale_value'   => $group->sum('sale_value'),
                'total_commission'   => $group->sum('commission_value'),
                'packages_count'     => $group->sum('packages_count'),
                'new_store_entries'  => $group->where('commission_type', 'new_store')->count(),
                'recurring_entries'  => $group->where('commission_type', 'recurring')->count(),
            ];
        });

        // Monthly evolution: group by year-month
        $evolution = $entries->groupBy(fn($e) => $e->order_date->format('Y-m'))
            ->map(fn($g) => [
                'month'            => $g->first()->order_date->format('M/Y'),
                'commission_value' => $g->sum('commission_value'),
                'sale_value'       => $g->sum('sale_value'),
            ])->values();

        return [
            'entries'          => $entries,
            'by_store'         => $byStore,
            'evolution'        => $evolution,
            'total_commission' => $entries->sum('commission_value'),
            'total_new_store'  => $entries->where('commission_type', 'new_store')->sum('commission_value'),
            'total_recurring'  => $entries->where('commission_type', 'recurring')->sum('commission_value'),
        ];
    }

    /**
     * Store performance report for a seller.
     */
    public function storeReport(Seller $seller, Carbon $from, Carbon $to): array
    {
        $stores = Store::where('seller_id', $seller->id)->get();

        $rows = $stores->map(function (Store $store) use ($seller, $from, $to) {
            $ledgerEntries = CommissionLedger::where('seller_id', $seller->id)
                ->where('store_id', $store->id)
                ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
                ->get();

            $hasOrders     = $ledgerEntries->isNotEmpty();
            $totalSales    = $ledgerEntries->sum('sale_value');
            $totalPackages = $ledgerEntries->sum('packages_count');
            $totalOrders   = $ledgerEntries->count();
            $totalComm     = $ledgerEntries->sum('commission_value');

            return [
                'store'          => $store,
                'registered_at'  => $store->created_at?->format('d/m/Y'),
                'seller_status'  => $store->seller_assignment_status,
                'has_orders'     => $hasOrders,
                'total_sales'    => (float) $totalSales,
                'total_orders'   => $totalOrders,
                'total_packages' => (int) $totalPackages,
                'commission'     => (float) $totalComm,
            ];
        });

        return [
            'stores'          => $rows->sortByDesc('commission')->values(),
            'total_active'    => $rows->where('has_orders', true)->count(),
            'total_inactive'  => $rows->where('has_orders', false)->count(),
            'total_stores'    => $rows->count(),
        ];
    }

    /**
     * Calculate goal progress percentages and forecasts.
     */
    public function goalProgress(
        Seller $seller,
        SellerGoal $goal,
        int $newStores,
        int $activeStores,
        int $packages
    ): array {
        $now = now();
        $daysInMonth = $now->daysInMonth;
        $daysPassed  = max(1, $now->day);

        $calc = function (bool $enabled, ?int $target, int $actual) use ($daysInMonth, $daysPassed): array {
            if (!$enabled || !$target) {
                return ['enabled' => false];
            }
            $pct = $target > 0 ? min(100, round($actual / $target * 100, 1)) : 0;
            $remaining = max(0, $target - $actual);
            // Daily rate
            $dailyRate = $daysPassed > 0 ? $actual / $daysPassed : 0;
            $projectedTotal = $dailyRate * $daysInMonth;
            $onTrack = $projectedTotal >= $target;
            $daysToHit = ($dailyRate > 0 && $remaining > 0)
                ? ceil($remaining / $dailyRate)
                : null;

            return [
                'enabled'       => true,
                'target'        => $target,
                'actual'        => $actual,
                'pct'           => $pct,
                'remaining'     => $remaining,
                'on_track'      => $onTrack,
                'days_to_hit'   => $daysToHit,
            ];
        };

        return [
            'new_stores'    => $calc($goal->new_stores_enabled,    $goal->new_stores_target,    $newStores),
            'active_stores' => $calc($goal->active_stores_enabled, $goal->active_stores_target, $activeStores),
            'packages'      => $calc($goal->packages_enabled,      $goal->packages_target,       $packages),
        ];
    }

    // ── Admin aggregates ────────────────────────────────────────────────────

    /**
     * Admin global dashboard: all sellers summary.
     */
    public function adminGlobalStats(Carbon $from, Carbon $to): array
    {
        $totalStores     = Store::whereNotNull('seller_id')->count();
        $approvedStores  = Store::where('seller_assignment_status', 'approved')->count();
        $activeSellers   = Seller::whereHas('stores', fn($q) => $q->where('seller_assignment_status', 'approved'))->count();

        $totalSales = CommissionLedger::whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->sum('sale_value');

        $paidComm = CommissionLedger::whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->where('status', 'paid')->sum('commission_value');

        $pendingComm = CommissionLedger::whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
            ->whereIn('status', ['pending', 'confirmed'])->sum('commission_value');

        $pendingClaims = \App\Models\SellerStoreClaim::where('status', 'pending')->count();
        $pendingStores = Store::whereNotNull('seller_id')->where('seller_assignment_status', 'pending')->count();

        return [
            'total_stores'    => $totalStores,
            'approved_stores' => $approvedStores,
            'active_sellers'  => $activeSellers,
            'total_sales'     => (float) $totalSales,
            'paid_commission' => (float) $paidComm,
            'pending_commission' => (float) $pendingComm,
            'pending_approvals' => $pendingClaims + $pendingStores,
        ];
    }

    /**
     * Admin: per-seller performance ranking for a period.
     */
    public function sellerPerformanceRanking(Carbon $from, Carbon $to): \Illuminate\Support\Collection
    {
        return Seller::with('user')
            ->get()
            ->map(function (Seller $seller) use ($from, $to) {
                $ledger = CommissionLedger::where('seller_id', $seller->id)
                    ->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])
                    ->selectRaw('
                        COUNT(*) as orders_count,
                        COALESCE(SUM(sale_value), 0) as revenue,
                        COALESCE(SUM(commission_value), 0) as commission,
                        COALESCE(SUM(packages_count), 0) as packages,
                        COUNT(DISTINCT store_id) as active_stores
                    ')
                    ->first();

                $totalStores    = Store::where('seller_id', $seller->id)->count();
                $approvedStores = Store::where('seller_id', $seller->id)->where('seller_assignment_status', 'approved')->count();
                $conversionRate = $totalStores > 0 ? round($approvedStores / $totalStores * 100, 1) : 0;

                return [
                    'seller'         => $seller,
                    'orders_count'   => (int)   $ledger->orders_count,
                    'revenue'        => (float)  $ledger->revenue,
                    'commission'     => (float)  $ledger->commission,
                    'packages'       => (int)    $ledger->packages,
                    'active_stores'  => (int)    $ledger->active_stores,
                    'total_stores'   => $totalStores,
                    'approved_stores'=> $approvedStores,
                    'conversion_pct' => $conversionRate,
                ];
            })
            ->sortByDesc('revenue')
            ->values();
    }
}
