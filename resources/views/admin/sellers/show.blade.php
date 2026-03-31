@extends('layouts.admin')

@section('title', 'Visualizar Vendedor')

@section('content')

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $seller->user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Detalhes do vendedor</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sellers.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i> Voltar
            </a>
            <a href="{{ route('admin.sellers.edit', $seller) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-pen-to-square text-xs"></i> Editar
            </a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nome</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->user->name }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Email</p>
            <p class="text-base font-semibold text-gray-900 truncate">{{ $seller->user->email }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Telefone</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->phone }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Comissão Novo Cliente</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->commission_new_client }}%</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Comissão Recorrente</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->commission_recurring }}%</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Meta Pacote Mensal</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->monthly_package_target }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Criado em</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Última atualização</p>
            <p class="text-base font-semibold text-gray-900">{{ $seller->updated_at->format('d/m/Y H:i') }}</p>
        </div>

    </div>

    {{-- Danger zone --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-red-100 p-5">
        <h3 class="text-sm font-semibold text-red-700 mb-3">Zona de perigo</h3>
        <form method="POST" action="{{ route('admin.sellers.destroy', $seller) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100 transition"
                onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">
                <i class="fas fa-trash text-xs"></i> Excluir vendedor
            </button>
        </form>
    </div>

@endsection
