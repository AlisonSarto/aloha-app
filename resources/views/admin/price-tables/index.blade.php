@extends('layouts.admin')

@section('title', 'Tabelas de Preços')

@section('content')

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tabelas de Preços</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure as faixas de preço por quantidade.</p>
        </div>
        <a href="{{ route('admin.price-tables.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition whitespace-nowrap">
            <i class="fas fa-plus text-xs"></i> Nova Tabela
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-green-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Nome</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Padrão</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($priceTables as $priceTable)
                    <tr class="hover:bg-green-50/40 transition-colors">
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $priceTable->name }}</td>
                        <td class="px-5 py-4">
                            @if($priceTable->is_default)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    <i class="fas fa-star text-xs"></i> Padrão
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500">Não</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('admin.price-tables.show', $priceTable) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.price-tables.edit', $priceTable) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>
                                @if(!$priceTable->is_default)
                                    <form action="{{ route('admin.price-tables.set-default', $priceTable) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 transition"
                                            title="Definir como padrão">
                                            <i class="fas fa-star text-xs"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.price-tables.destroy', $priceTable) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja deletar esta tabela?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-14 text-center">
                            <i class="fas fa-table text-4xl text-gray-200 block mb-3"></i>
                            <span class="text-sm text-gray-400">Nenhuma tabela de preço encontrada.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $priceTables->links() }}</div>

@endsection
