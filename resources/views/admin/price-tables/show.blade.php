@extends('layouts.admin')

@section('title', 'Detalhes da Tabela de Preços')

@section('content')

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $priceTable->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">
                Tabela de preços
                @if($priceTable->is_default)
                    <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                        <i class="fas fa-star text-xs"></i> Padrão
                    </span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.price-tables.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i> Voltar
            </a>
            <a href="{{ route('admin.price-tables.edit', $priceTable) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-pen-to-square text-xs"></i> Editar
            </a>
        </div>
    </div>

    {{-- Price ranges table --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Faixas de Preço</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-green-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Qtd. Mínima</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Qtd. Máxima</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Preço Unitário</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($priceTable->ranges as $range)
                    <tr class="hover:bg-green-50/40 transition-colors">
                        <td class="px-5 py-4 text-sm text-gray-900">{{ $range->min_quantity }}</td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            {{ $range->max_quantity ? $range->max_quantity : '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm font-semibold text-gray-900">
                            R$ {{ number_format($range->unit_price, 2, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-14 text-center">
                            <i class="fas fa-table text-4xl text-gray-200 block mb-3"></i>
                            <span class="text-sm text-gray-400">Nenhuma faixa definida.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
