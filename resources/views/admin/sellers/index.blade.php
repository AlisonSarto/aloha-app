@extends('layouts.admin')

@section('title', 'Vendedores')

@section('content')
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">
            Vendedores
        </h1>
        <a href="{{ route('admin.sellers.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Criar Vendedor
        </a>
    </div>

    <!-- Busca -->
    <form method="GET" class="mb-6">
        <div class="flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Pesquisar vendedor..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >

            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Buscar
            </button>
        </div>
    </form>

    <!-- Tabela -->
    <div class="overflow-hidden">
        <table class="min-w-full border border-gray-400 border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Nome</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Email</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Telefone</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($sellers as $seller)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border border-gray-300 px-4 py-3 text-sm font-medium">{{ $seller->user->name }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-sm">{{ $seller->user->email }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-sm">{{ $seller->phone }}</td>
                        <td class="border border-gray-300 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <!-- Visualizar -->
                                <a href="{{ route('admin.sellers.show', $seller) }}"
                                   class="px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <!-- Editar -->
                                <a href="{{ route('admin.sellers.edit', $seller) }}"
                                   class="px-3 py-1.5 text-sm font-medium text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <!-- Excluir -->
                                <form method="POST" action="{{ route('admin.sellers.destroy', $seller) }}" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition"
                                            onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                            Nenhum resultado encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mensagens -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Paginação -->
    <div class="mt-6">
        {{ $sellers->links() }}
    </div>
@endsection
