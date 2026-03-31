@extends('layouts.admin')

@section('title', 'Visualizar Usuário')

@section('content')

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Detalhes do usuário</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left text-xs"></i> Voltar
            </a>
            <a href="{{ route('admin.users.edit', $user) }}"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-pen-to-square text-xs"></i> Editar
            </a>
        </div>
    </div>

    {{-- Info cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Nome</p>
            <p class="text-base font-semibold text-gray-900">{{ $user->name }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Email</p>
            <p class="text-base font-semibold text-gray-900 truncate">{{ $user->email }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Criado em</p>
            <p class="text-base font-semibold text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1">Última atualização</p>
            <p class="text-base font-semibold text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
        </div>

    </div>

    {{-- Danger zone --}}
    @if($user->id !== auth()->id())
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-red-100 p-5">
            <h3 class="text-sm font-semibold text-red-700 mb-3">Zona de perigo</h3>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-red-50 px-4 py-2.5 text-sm font-medium text-red-700 hover:bg-red-100 transition"
                    onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                    <i class="fas fa-trash text-xs"></i> Excluir usuário
                </button>
            </form>
        </div>
    @endif

@endsection
