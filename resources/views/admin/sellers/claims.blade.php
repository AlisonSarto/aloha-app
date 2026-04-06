@extends('layouts.admin')

@section('title', 'Aprovações de Lojas')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Aprovações de Lojas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gerencie lojas pendentes e solicitações de vendedores</p>
        </div>
        <a href="{{ route('admin.commissions.index') }}"
           class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-coins text-xs mr-1"></i> Comissões
        </a>
    </div>

    {{-- Status filter --}}
    <div class="flex gap-2 mb-6">
        @foreach(['pending' => 'Pendentes', 'approved' => 'Aprovadas', 'rejected' => 'Rejeitadas', 'all' => 'Todas'] as $val => $label)
        <a href="{{ route('admin.sellers.claims', ['status' => $val]) }}"
           class="rounded-lg px-4 py-2 text-sm font-medium transition {{ $status === $val ? 'bg-green-600 text-white shadow-sm' : 'bg-white ring-1 ring-black/5 text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    {{-- Pending new stores (newly registered by sellers) --}}
    @if($pendingStores->isNotEmpty())
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-amber-900 mb-3">
            <i class="fas fa-clock mr-1"></i>
            Novas lojas aguardando aprovação ({{ $pendingStores->count() }})
        </h2>
        <div class="space-y-3">
            @foreach($pendingStores as $store)
            <div class="flex items-center justify-between gap-4 rounded-lg bg-white p-4 ring-1 ring-amber-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $store->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Vendedor: {{ $store->seller->user->name }} · Cadastrada {{ $store->created_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $store->address_city }}/{{ $store->address_state }}</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('admin.sellers.stores.approve', $store) }}">
                        @csrf
                        <button type="submit" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700 transition">
                            <i class="fas fa-check mr-1"></i> Aprovar
                        </button>
                    </form>
                    <button type="button" onclick="openRejectStoreModal({{ $store->id }})"
                        class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 transition">
                        <i class="fas fa-times mr-1"></i> Rejeitar
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Claims table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Solicitações de vínculo (lojas existentes)</h2>
        </div>
        @if($claims->isEmpty())
            <p class="px-5 py-8 text-sm text-gray-400 text-center">Nenhuma solicitação {{ $status !== 'all' ? 'com status "'.$status.'"' : '' }}.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500">
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-left">Loja</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-left">Solicitado em</th>
                        <th class="px-4 py-3 text-left">Revisado por</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($claims as $claim)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $claim->seller->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $claim->store->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ match($claim->status) { 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700', default => 'bg-amber-100 text-amber-700' } }}">
                                {{ match($claim->status) { 'approved' => 'Aprovada', 'rejected' => 'Rejeitada', default => 'Pendente' } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $claim->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $claim->reviewer?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($claim->status === 'pending')
                            <div class="flex items-center justify-center gap-2">
                                <form method="POST" action="{{ route('admin.sellers.claims.approve', $claim) }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-green-600 hover:underline font-medium">Aprovar</button>
                                </form>
                                <button type="button" onclick="openRejectClaimModal({{ $claim->id }})"
                                    class="text-xs text-red-600 hover:underline font-medium">Rejeitar</button>
                            </div>
                            @else
                                @if($claim->rejection_reason)
                                    <span class="text-xs text-gray-400" title="{{ $claim->rejection_reason }}">Ver motivo</span>
                                @else — @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{ $claims->links() }}

    {{-- Reject Claim Modal --}}
    <div id="reject-claim-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Rejeitar Solicitação</h3>
            <form method="POST" id="reject-claim-form" action="">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da rejeição</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Informe o motivo..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-green-500 focus:ring-green-500 shadow-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">Rejeitar</button>
                    <button type="button" onclick="closeModal('reject-claim-modal')" class="flex-1 rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Reject Store Modal --}}
    <div id="reject-store-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Rejeitar Vínculo de Loja</h3>
            <form method="POST" id="reject-store-form" action="">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da rejeição</label>
                    <textarea name="rejection_reason" rows="3" required placeholder="Informe o motivo..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-green-500 focus:ring-green-500 shadow-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition">Rejeitar</button>
                    <button type="button" onclick="closeModal('reject-store-modal')" class="flex-1 rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectClaimModal(id) {
            document.getElementById('reject-claim-form').action = '/admin/sellers/claims/' + id + '/reject';
            const m = document.getElementById('reject-claim-modal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
        function openRejectStoreModal(id) {
            document.getElementById('reject-store-form').action = '/admin/sellers/stores/' + id + '/reject';
            const m = document.getElementById('reject-store-modal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
        function closeModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden'); m.classList.remove('flex');
        }
    </script>

@endsection
