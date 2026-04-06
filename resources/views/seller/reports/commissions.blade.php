@extends('layouts.seller')

@section('title', 'Relatório de Comissões')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Relatório de Comissões</h1>
            <p class="text-sm text-gray-500 mt-0.5">Comissões por período e loja</p>
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
            <label class="block text-xs font-medium text-gray-600 mb-1">Loja</label>
            <select name="store_id" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500">
                <option value="">Todas</option>
                @foreach($stores as $s)
                    <option value="{{ $s->id }}" {{ $storeId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition shadow-sm">
            Filtrar
        </button>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Total comissão</p>
            <p class="text-xl font-bold text-green-700">R$ {{ number_format($report['total_commission'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissão nova loja</p>
            <p class="text-xl font-bold text-gray-900">R$ {{ number_format($report['total_new_store'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissão recorrente</p>
            <p class="text-xl font-bold text-gray-900">R$ {{ number_format($report['total_recurring'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Pedidos no período</p>
            <p class="text-xl font-bold text-gray-900">{{ $report['entries']->count() }}</p>
        </div>
    </div>

    {{-- Evolution chart data (simple bar via CSS) --}}
    @if($report['evolution']->isNotEmpty())
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Evolução mensal</h2>
        @php $maxComm = $report['evolution']->max('commission_value') ?: 1; @endphp
        <div class="flex items-end gap-2 h-24">
            @foreach($report['evolution'] as $ev)
                @php $h = max(4, round($ev['commission_value'] / $maxComm * 80)); @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs text-gray-500">R${{ number_format($ev['commission_value'],0,',','.') }}</span>
                    <div class="w-full rounded-t bg-green-500" style="height: {{ $h }}px"></div>
                    <span class="text-xs text-gray-400">{{ $ev['month'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- By Store --}}
    @if($report['by_store']->isNotEmpty())
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Por loja</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500">
                        <th class="pb-2 text-left">Loja</th>
                        <th class="pb-2 text-right">Pedidos</th>
                        <th class="pb-2 text-right">Venda total</th>
                        <th class="pb-2 text-right">Comissão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($report['by_store']->sortByDesc('total_commission') as $row)
                    <tr>
                        <td class="py-2.5 font-medium text-gray-900">{{ $row['store']->name }}</td>
                        <td class="py-2.5 text-right text-gray-600">{{ $row['entries_count'] }}</td>
                        <td class="py-2.5 text-right text-gray-600">R$ {{ number_format($row['total_sale_value'], 2, ',', '.') }}</td>
                        <td class="py-2.5 text-right font-semibold text-green-700">R$ {{ number_format($row['total_commission'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Entries --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Detalhes dos pedidos</h2>
        @if($report['entries']->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Nenhum pedido no período.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500">
                        <th class="pb-2 text-left">Data</th>
                        <th class="pb-2 text-left">Loja</th>
                        <th class="pb-2 text-right">Venda</th>
                        <th class="pb-2 text-right">Tipo</th>
                        <th class="pb-2 text-right">Taxa</th>
                        <th class="pb-2 text-right">Comissão</th>
                        <th class="pb-2 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($report['entries'] as $entry)
                    <tr>
                        <td class="py-2.5 text-gray-600">{{ $entry->order_date->format('d/m/Y') }}</td>
                        <td class="py-2.5 font-medium text-gray-900">{{ $entry->store->name }}</td>
                        <td class="py-2.5 text-right text-gray-600">R$ {{ number_format($entry->sale_value, 2, ',', '.') }}</td>
                        <td class="py-2.5 text-right">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $entry->commission_type === 'new_store' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $entry->commission_type === 'new_store' ? 'Nova loja' : 'Recorrente' }}
                            </span>
                        </td>
                        <td class="py-2.5 text-right text-gray-600">{{ $entry->commission_rate }}%</td>
                        <td class="py-2.5 text-right font-semibold text-green-700">R$ {{ number_format($entry->commission_value, 2, ',', '.') }}</td>
                        <td class="py-2.5 text-right">
                            <span class="text-xs px-2 py-0.5 rounded-full {{ match($entry->status) { 'paid' => 'bg-green-100 text-green-700', 'confirmed' => 'bg-blue-100 text-blue-700', default => 'bg-amber-100 text-amber-700' } }}">
                                {{ match($entry->status) { 'paid' => 'Pago', 'confirmed' => 'Confirmado', default => 'Pendente' } }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

@endsection
