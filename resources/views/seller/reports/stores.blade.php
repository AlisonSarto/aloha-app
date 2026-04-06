@extends('layouts.seller')

@section('title', 'Relatório de Lojas')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Relatório de Lojas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Desempenho por loja no período</p>
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
        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition shadow-sm">
            Filtrar
        </button>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Total de lojas</p>
            <p class="text-xl font-bold text-gray-900">{{ $report['total_stores'] }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Lojas ativas</p>
            <p class="text-xl font-bold text-green-700">{{ $report['total_active'] }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Sem vendas</p>
            <p class="text-xl font-bold text-amber-600">{{ $report['total_inactive'] }}</p>
        </div>
    </div>

    {{-- Store ranking table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Ranking por comissão</h2>
        @if($report['stores']->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Nenhuma loja cadastrada ainda.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500">
                        <th class="pb-2 text-left">#</th>
                        <th class="pb-2 text-left">Loja</th>
                        <th class="pb-2 text-center">Status</th>
                        <th class="pb-2 text-right">Pedidos</th>
                        <th class="pb-2 text-right">Venda total</th>
                        <th class="pb-2 text-right">Pacotes</th>
                        <th class="pb-2 text-right">Comissão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($report['stores'] as $i => $row)
                    <tr class="{{ !$row['has_orders'] ? 'bg-amber-50/50' : '' }}">
                        <td class="py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="py-3">
                            <p class="font-medium text-gray-900">{{ $row['store']->name }}</p>
                            <p class="text-xs text-gray-400">Desde {{ $row['registered_at'] }}</p>
                        </td>
                        <td class="py-3 text-center">
                            @if(!$row['has_orders'])
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                                    <i class="fas fa-exclamation-circle text-xs"></i> Sem vendas
                                </span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Ativa</span>
                            @endif
                        </td>
                        <td class="py-3 text-right text-gray-600">{{ $row['total_orders'] }}</td>
                        <td class="py-3 text-right text-gray-600">R$ {{ number_format($row['total_sales'], 2, ',', '.') }}</td>
                        <td class="py-3 text-right text-gray-600">{{ $row['total_packages'] }}</td>
                        <td class="py-3 text-right font-semibold {{ $row['commission'] > 0 ? 'text-green-700' : 'text-gray-400' }}">
                            R$ {{ number_format($row['commission'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

@endsection
