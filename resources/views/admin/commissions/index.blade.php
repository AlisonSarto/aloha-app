@extends('layouts.admin')

@section('title', 'Comissões')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Gestão de Comissões</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visualize, aprove e ajuste comissões de vendedores</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.commissions.dashboard') }}"
               class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
                <i class="fas fa-chart-bar text-xs mr-1"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- Global Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Total vendas</p>
            <p class="text-lg font-bold text-gray-900">R$ {{ number_format($globalStats['total_sales'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissão gerada</p>
            <p class="text-lg font-bold text-green-700">R$ {{ number_format($globalStats['pending_commission'] + $globalStats['paid_commission'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissão paga</p>
            <p class="text-lg font-bold text-gray-900">R$ {{ number_format($globalStats['paid_commission'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-amber-50 ring-1 ring-amber-200 p-4 rounded-xl">
            <p class="text-xs text-amber-700 mb-1">Aprovações pendentes</p>
            <p class="text-lg font-bold text-amber-800">{{ $globalStats['pending_approvals'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">De</label>
            <input type="date" name="from" value="{{ $from->toDateString() }}"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Até</label>
            <input type="date" name="to" value="{{ $to->toDateString() }}"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Vendedor</label>
            <select name="seller_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Todos</option>
                @foreach($sellers as $s)
                    <option value="{{ $s->id }}" {{ $sellerId == $s->id ? 'selected' : '' }}>{{ $s->user->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Loja</label>
            <select name="store_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Todas</option>
                @foreach($stores as $s)
                    <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Todos</option>
                <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pendente</option>
                <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmado</option>
                <option value="paid"      {{ $status === 'paid'      ? 'selected' : '' }}>Pago</option>
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">Filtrar</button>
    </form>

    {{-- Bulk actions --}}
    <form id="bulk-form" method="POST" action="{{ route('admin.commissions.mark-paid') }}" class="mb-3">
        @csrf
        <div id="bulk-bar" class="hidden rounded-xl bg-green-50 border border-green-200 px-4 py-3 mb-3 items-center gap-3">
            <span id="selected-count" class="text-sm text-green-800 font-medium"></span>
            <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                <i class="fas fa-check mr-1"></i> Marcar como Pagas
            </button>
        </div>

    {{-- Table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500">
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="h-4 w-4 rounded text-green-600"/>
                        </th>
                        <th class="px-4 py-3 text-left">Data</th>
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-left">Loja</th>
                        <th class="px-4 py-3 text-right">Venda</th>
                        <th class="px-4 py-3 text-center">Tipo</th>
                        <th class="px-4 py-3 text-right">Taxa</th>
                        <th class="px-4 py-3 text-right">Comissão</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ledgers as $entry)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3">
                            @if($entry->status !== 'paid')
                                <input type="checkbox" name="ids[]" value="{{ $entry->id }}" class="row-check h-4 w-4 rounded text-green-600"/>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->order_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $entry->seller->user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $entry->store->name }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">R$ {{ number_format($entry->sale_value, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $entry->commission_type === 'new_store' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $entry->commission_type === 'new_store' ? 'Nova' : 'Recorrente' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $entry->commission_rate }}%</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">R$ {{ number_format($entry->commission_value, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ match($entry->status) { 'paid' => 'bg-green-100 text-green-700', 'confirmed' => 'bg-blue-100 text-blue-700', default => 'bg-amber-100 text-amber-700' } }}">
                                {{ match($entry->status) { 'paid' => 'Pago', 'confirmed' => 'Confirmado', default => 'Pendente' } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="openAdjustModal({{ $entry->id }}, {{ $entry->commission_value }})"
                                class="text-xs text-green-600 hover:underline font-medium">Ajustar</button>
                            <button type="button" onclick="deleteCommission({{ $entry->id }})"
                                class="text-xs text-red-500 hover:underline font-medium ml-2">Deletar</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-400">Nenhum registro no período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </form>

    {{ $ledgers->links() }}

    {{-- Adjust modal --}}
    <div id="adjust-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 class="text-base font-semibold text-gray-900 mb-4">Ajustar Comissão</h3>
            <form method="POST" id="adjust-form" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Novo valor (R$)</label>
                    <input type="number" name="adjusted_value" id="adjust-value" step="0.01" min="0" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base focus:border-green-500 focus:ring-green-500 shadow-sm"/>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da correção</label>
                    <textarea name="reason" rows="3" required placeholder="Descreva o motivo..."
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm focus:border-green-500 focus:ring-green-500 shadow-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition">Salvar Ajuste</button>
                    <button type="button" onclick="closeAdjustModal()" class="flex-1 rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-300 transition">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Bulk select
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
            updateBulkBar();
        });
        document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateBulkBar));
        function updateBulkBar() {
            const checked = document.querySelectorAll('.row-check:checked').length;
            const bar = document.getElementById('bulk-bar');
            bar.classList.toggle('hidden', checked === 0);
            bar.classList.toggle('flex', checked > 0);
            document.getElementById('selected-count').textContent = checked + ' selecionado(s)';
        }

        // Adjust modal
        function openAdjustModal(id, currentValue) {
            document.getElementById('adjust-form').action = '/admin/commissions/' + id + '/adjust';
            document.getElementById('adjust-value').value = currentValue;
            const m = document.getElementById('adjust-modal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeAdjustModal() {
            const m = document.getElementById('adjust-modal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
        // Delete commission
        function deleteCommission(id) {
            if (!confirm('Tem certeza que deseja deletar esta comissão? Esta ação não pode ser desfeita.')) return;
            fetch('/admin/commissions/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            }).then(r => { if (r.ok || r.redirected) window.location.reload(); });
        }
    </script>

@endsection
