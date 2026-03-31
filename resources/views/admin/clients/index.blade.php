@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
            <p class="text-sm text-gray-500 mt-0.5">Gerencie os clientes e vincule seus comércios.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="mb-5 rounded-xl bg-white shadow-sm ring-1 ring-black/5 px-4 py-3">
        <form method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Pesquisar cliente..."
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
            </div>
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition shadow-sm">
                <i class="fas fa-magnifying-glass text-xs"></i> Buscar
            </button>
        </form>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 overflow-hidden overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-green-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Nome</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Telefone</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($clients as $client)
                    <tr class="hover:bg-green-50/40 transition-colors">
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $client->user->name }}</td>
                        <td class="px-5 py-4 text-sm text-gray-600">{{ $client->phone }}</td>
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.clients.show', $client) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 transition">
                                <i class="fas fa-link text-xs"></i> Vincular comércios
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-14 text-center">
                            <i class="fas fa-users text-4xl text-gray-200 block mb-3"></i>
                            <span class="text-sm text-gray-400">Nenhum cliente encontrado.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $clients->links() }}</div>

@endsection
