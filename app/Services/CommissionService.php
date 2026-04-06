<?php

namespace App\Services;

use App\Models\CommissionLedger;
use App\Models\Seller;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Record a commission for an order.
     * Idempotent: does nothing if gestao_click_order_id already has an entry for this seller.
     */
    public function recordOrderCommission(
        Store  $store,
        string $gestaoClickOrderId,
        float  $saleValue,
        int    $packagesCount,
        Carbon $orderDate
    ): ?CommissionLedger {
        if (!$store->isSellerApproved()) {
            return null;
        }

        $seller = $store->seller;
        if (!$seller) {
            return null;
        }

        // Idempotency: skip if already recorded
        $exists = CommissionLedger::where('seller_id', $seller->id)
            ->where('gestao_click_order_id', $gestaoClickOrderId)
            ->exists();

        if ($exists) {
            return null;
        }

        $commissionType = $this->resolveCommissionType($store, $seller);
        $rate           = $commissionType === 'new_store'
            ? $seller->commission_new_client
            : $seller->commission_recurring;

        $commissionValue = round($saleValue * ($rate / 100), 2);

        return CommissionLedger::create([
            'seller_id'              => $seller->id,
            'store_id'               => $store->id,
            'gestao_click_order_id'  => $gestaoClickOrderId,
            'order_date'             => $orderDate->toDateString(),
            'sale_value'             => $saleValue,
            'packages_count'         => $packagesCount,
            'commission_type'        => $commissionType,
            'commission_rate'        => $rate,
            'commission_value'       => $commissionValue,
            'status'                 => 'pending',
        ]);
    }

    /**
     * Determine whether a store is in the "new_store" commission phase.
     * A client is considered new for the first NEW_CLIENT_ORDERS orders;
     * from the (N+1)-th order onward they are "recurring".
     */
    public function resolveCommissionType(Store $store, Seller $seller): string
    {
        $completedOrders = CommissionLedger::where('seller_id', $seller->id)
            ->where('store_id', $store->id)
            ->count();

        return $completedOrders < Seller::NEW_CLIENT_ORDERS ? 'new_store' : 'recurring';
    }

    /**
     * Admin: adjust a commission value directly on the ledger.
     */
    public function adjustCommission(
        CommissionLedger $ledger,
        float            $newValue,
        string           $reason,
        int              $adminUserId
    ): CommissionLedger {
        $ledger->update([
            'commission_value' => $newValue,
            'notes'            => $reason,
        ]);

        return $ledger;
    }

    /**
     * Admin: mark a set of commission ledger entries as paid.
     */
    public function markPaid(array $ledgerIds, int $adminUserId): int
    {
        return CommissionLedger::whereIn('id', $ledgerIds)
            ->whereNotIn('status', ['paid', 'canceled'])
            ->update(['status' => 'paid', 'paid_at' => now()]);
    }

    /**
     * Admin: cancel a commission entry, keeping it for audit purposes.
     */
    public function cancelCommission(CommissionLedger $ledger, string $reason, int $adminUserId): CommissionLedger
    {
        $ledger->update([
            'status'        => 'canceled',
            'canceled_by'   => $adminUserId,
            'cancel_reason' => $reason,
        ]);

        return $ledger;
    }

    /**
     * Standalone manual adjustment: creates a ledger entry of type 'manual'.
     */
    public function standaloneAdjustment(
        Seller $seller,
        float  $adjustedValue,
        string $reason,
        int    $adminUserId,
        Store  $store
    ): CommissionLedger {
        return CommissionLedger::create([
            'seller_id'       => $seller->id,
            'store_id'        => $store->id,
            'order_date'      => now()->toDateString(),
            'sale_value'      => 0,
            'packages_count'  => 0,
            'commission_type' => 'manual',
            'commission_rate' => 0,
            'commission_value' => $adjustedValue,
            'status'          => 'pending',
            'notes'           => $reason,
        ]);
    }
}
