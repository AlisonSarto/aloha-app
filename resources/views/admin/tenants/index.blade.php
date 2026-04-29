@extends('layouts.admin')

@section('title', 'Unidades')

@section('content')

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Unidades</h1>
            <p class="text-sm text-gray-500 mt-0.5">Visualize e edite os dados das unidades cadastradas.</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition whitespace-nowrap">
            <i class="fas fa-plus text-xs"></i> Criar Unidade
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <p class="font-medium">{{ session('success') }}</p>

            @if (session('generated_admin_email') && session('generated_admin_password'))
                <div class="mt-2 border-t border-green-200 pt-2 text-xs text-green-900">
                    <p><strong>Email admin:</strong> {{ session('generated_admin_email') }}</p>
                    <p><strong>Senha temporária:</strong> {{ session('generated_admin_password') }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="mb-5 rounded-xl bg-white shadow-sm ring-1 ring-black/5 px-4 py-3">
        <form method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Pesquisar unidades..."
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
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-green-800 uppercase tracking-wide">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($tenants as $tenant)
                    <tr class="hover:bg-green-50/40 transition-colors">
                        <td class="px-5 py-4 text-sm font-medium text-gray-900">{{ $tenant->name }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-1.5">
                                <a href="./tenants/{{ $tenant->id }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition"
                                    title="Visualizar">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="./tenants/{{ $tenant->id }}/edit"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-700 bg-amber-50 hover:bg-amber-100 transition"
                                    title="Editar">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-14 text-center">
                            <i class="fas fa-tenant text-4xl text-gray-200 block mb-3"></i>
                            <span class="text-sm text-gray-400">Nenhuma unidade encontrada</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $tenants->links() }}</div>

@endsection
