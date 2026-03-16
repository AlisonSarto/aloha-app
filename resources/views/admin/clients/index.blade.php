@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
    <h1 class="text-3xl font-bold mb-4">
        Clientes
    </h1>

    <!-- Busca -->
    <form method="GET" class="mb-6">
        <div class="flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Pesquisar usuário..."
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
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Telefone</th>
                    <th class="border border-gray-300 px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Ações</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                @forelse ($clients as $client)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border border-gray-300 px-4 py-3 text-sm font-medium">{{ $client->user->name }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-sm">{{ $client->phone }}</td>
                        <td class="border border-gray-300 px-4 py-3">
                            <div class="flex items-center gap-2">

                                <!-- Visualizar / vincular -->
                                <a href="{{ route('admin.clients.show', $client) }}"
                                   class="px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                                    Vincular comércios
                                </a>

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

    <!-- Mensagens -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Paginação -->
    <div class="mt-6">
        {{ $clients->links() }}
    </div>


@endsection
