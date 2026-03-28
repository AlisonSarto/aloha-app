@extends('layouts.admin')

@section('title', 'Detalhes da Tabela de Preços')

@section('content')
    <h1 class="text-3xl font-bold mb-4">
        Detalhes da Tabela de Preços: {{ $priceTable->name }}
    </h1>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Informações Gerais</h2>
        <p><strong>Nome:</strong> {{ $priceTable->name }}</p>
        <p><strong>Padrão:</strong> {{ $priceTable->is_default ? 'Sim' : 'Não' }}</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h2 class="text-xl font-semibold mb-4">Faixas de Preço</h2>
        <table class="min-w-full border border-gray-400 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Quantidade Mínima</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Quantidade Máxima</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Preço Unitário</th>
                </tr>
            </thead>
            <tbody>
                @forelse($priceTable->ranges as $range)
                    <tr>
                        <td class="border border-gray-300 px-4 py-3">{{ $range->min_quantity }}</td>
                        <td class="border border-gray-300 px-4 py-3">{{ $range->max_quantity ?: 'Ilimitado' }}</td>
                        <td class="border border-gray-300 px-4 py-3">R$ {{ number_format($range->unit_price, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                            Nenhuma faixa definida.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.price-tables.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">
            Voltar
        </a>
        <a href="{{ route('admin.price-tables.edit', $priceTable) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
            Editar
        </a>
    </div>
@endsection
