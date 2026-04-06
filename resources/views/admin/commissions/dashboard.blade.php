@extends('layouts.admin')

@section('title', 'Dashboard de Vendedores')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Dashboard de Vendedores</h1>
            <p class="text-sm text-gray-500 mt-0.5">Performance e ranking por período</p>
        </div>
        <a href="{{ route('admin.commissions.index') }}"
           class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-list text-xs mr-1"></i> Comissões
        </a>
    </div>

    {{-- Period filter --}}
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
        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">Filtrar</button>
    </form>

    {{-- Global KPIs --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Vendedores ativos</p>
            <p class="text-xl font-bold text-gray-900">{{ $globalStats['active_sellers'] }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Lojas aprovadas</p>
            <p class="text-xl font-bold text-green-700">{{ $globalStats['approved_stores'] }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Total vendas</p>
            <p class="text-xl font-bold text-gray-900">R$ {{ number_format($globalStats['total_sales'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Aprovações pendentes</p>
            <p class="text-xl font-bold text-amber-600">{{ $globalStats['pending_approvals'] }}</p>
        </div>
    </div>

    {{-- Seller ranking --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Ranking de Vendedores</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50 text-xs text-gray-500">
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Vendedor</th>
                        <th class="px-4 py-3 text-right">Lojas</th>
                        <th class="px-4 py-3 text-right">Lojas ativas</th>
                        <th class="px-4 py-3 text-right">Conversão</th>
                        <th class="px-4 py-3 text-right">Receita</th>
                        <th class="px-4 py-3 text-right">Comissão</th>
                        <th class="px-4 py-3 text-right">Pacotes</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($ranking as $i => $row)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-gray-400 text-xs font-medium">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $row['seller']->user->name }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $row['total_stores'] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $row['active_stores'] }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="{{ $row['conversion_pct'] >= 70 ? 'text-green-700 font-semibold' : ($row['conversion_pct'] >= 40 ? 'text-amber-700' : 'text-red-600') }}">
                                {{ $row['conversion_pct'] }}%
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">R$ {{ number_format($row['revenue'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">R$ {{ number_format($row['commission'], 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $row['packages'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.sellers.show', $row['seller']) }}"
                               class="text-xs text-green-600 hover:underline font-medium">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-400">Nenhum vendedor no período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
