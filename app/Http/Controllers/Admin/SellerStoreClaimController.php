<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\SellerGoal;
use App\Models\SellerStoreClaim;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerStoreClaimController extends Controller
{
    /** List all pending claims + recently reviewed */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $claims = SellerStoreClaim::with(['seller.user', 'store', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->paginate(20)->withQueryString();

        // Also show stores pending seller approval (new stores registered by sellers)
        $pendingStores = Store::with(['seller.user'])
            ->whereNotNull('seller_id')
            ->where('seller_assignment_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sellers.claims', compact('claims', 'pendingStores', 'status'));
    }

    /** Approve a claim: assign seller_id to store */
    public function approveClaim(SellerStoreClaim $claim)
    {
        abort_if($claim->status !== 'pending', 400, 'Solicitação já processada.');

        DB::transaction(function () use ($claim) {
            $claim->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            $claim->store->update([
                'seller_id'                       => $claim->seller_id,
                'seller_assignment_status'        => 'approved',
                'seller_assignment_approved_by'   => auth()->id(),
                'seller_assignment_approved_at'   => now(),
            ]);
        });

        return back()->with('success', 'Solicitação aprovada. Loja vinculada ao vendedor e comissões ativadas.');
    }

    /** Reject a claim */
    public function rejectClaim(Request $request, SellerStoreClaim $claim)
    {
        $request->validate(['rejection_reason' => 'required|string|min:5']);
        abort_if($claim->status !== 'pending', 400, 'Solicitação já processada.');

        $claim->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Solicitação rejeitada.');
    }

    /** Approve a store's seller link (new stores pending) */
    public function approveStore(Store $store)
    {
        abort_if($store->seller_assignment_status !== 'pending', 400, 'Loja não está pendente de aprovação.');

        $store->update([
            'seller_assignment_status'      => 'approved',
            'seller_assignment_approved_by' => auth()->id(),
            'seller_assignment_approved_at' => now(),
        ]);

        return back()->with('success', 'Loja aprovada para o vendedor. Comissões ativadas a partir de agora.');
    }

    /** Reject a store's seller link */
    public function rejectStore(Request $request, Store $store)
    {
        $request->validate(['rejection_reason' => 'required|string|min:5']);
        abort_if($store->seller_assignment_status !== 'pending', 400, 'Loja não está pendente.');

        $store->update([
            'seller_assignment_status'      => 'rejected',
            'seller_assignment_reason'      => $request->rejection_reason,
            'seller_assignment_approved_by' => auth()->id(),
            'seller_assignment_approved_at' => now(),
        ]);

        return back()->with('success', 'Vínculo rejeitado.');
    }

    /** Admin: configure goals for a specific seller/month */
    public function goals(Request $request, Seller $seller)
    {
        $year  = (int) $request->input('year',  now()->year);
        $month = (int) $request->input('month', now()->month);
        $goal  = SellerGoal::forSellerMonth($seller->id, $year, $month);

        return view('admin.sellers.goals', compact('seller', 'goal', 'year', 'month'));
    }

    public function updateGoals(Request $request, Seller $seller)
    {
        $year  = (int) $request->input('year',  now()->year);
        $month = (int) $request->input('month', now()->month);

        $validated = $request->validate([
            'new_stores_enabled'    => 'boolean',
            'active_stores_enabled' => 'boolean',
            'packages_enabled'      => 'boolean',
            'new_stores_target'     => 'nullable|integer|min:1',
            'active_stores_target'  => 'nullable|integer|min:1',
            'packages_target'       => 'nullable|integer|min:1',
        ]);

        SellerGoal::updateOrCreate(
            ['seller_id' => $seller->id, 'year' => $year, 'month' => $month],
            $validated
        );

        return redirect()
            ->route('admin.sellers.show', $seller)
            ->with('success', 'Metas do vendedor atualizadas.');
    }
}
