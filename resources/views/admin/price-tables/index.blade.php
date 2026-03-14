@extends('layouts.admin')

@section('title', 'Tabelas de Preços')

@section('content')
    <h1 class="text-3xl font-bold mb-4">
        Tabelas de Preços
    </h1>

    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.price-tables.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Criar Nova Tabela
        </a>
    </div>

    <!-- Tabela -->
    <div class="overflow-hidden">
        <table class="min-w-full border border-gray-400 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nome</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Padrão</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($priceTables as $priceTable)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border border-gray-300 px-4 py-3 text-sm font-medium">{{ $priceTable->name }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-sm">
                            @if($priceTable->is_default)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Sim</span>
                            @else
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Não</span>
                            @endif
                        </td>
                        <td class="border border-gray-300 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <!-- Visualizar -->
                                <a href="{{ route('admin.price-tables.show', $priceTable) }}"
                                    class="px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.price-tables.edit', $priceTable) }}"
                                    class="px-3 py-1.5 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <!-- Definir como padrão -->
                                @if(!$priceTable->is_default)
                                    <form action="{{ route('admin.price-tables.set-default', $priceTable) }}" method="POST" class="inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                                            <i class="fa-solid fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <!-- Deletar -->
                                @if(!$priceTable->is_default)
                                    <form action="{{ route('admin.price-tables.destroy', $priceTable) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja deletar esta tabela?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-6 text-center text-gray-500">
                            Nenhum resultado encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="mt-6">
        {{ $priceTables->links() }}
    </div>

@endsection
