@extends('layouts.admin')

@section('title', 'Criar Vendedor')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Criar Vendedor
</h1>

<form method="POST" action="{{ route('admin.sellers.store') }}">
    @csrf

    <div class="bg-white border border-gray-300 rounded-lg p-6 space-y-6">

        <!-- Nome -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nome
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Senha -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Senha
            </label>

            <input
                type="password"
                name="password"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirmar Senha -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Confirmar Senha
            </label>

            <input
                type="password"
                name="password_confirmation"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
        </div>

        <!-- Telefone -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Telefone
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comissão Novo Cliente -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Comissão Novo Cliente (%)
            </label>

            <input
                type="number"
                step="0.01"
                name="commission_new_client"
                value="{{ old('commission_new_client', 5) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('commission_new_client')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Comissão Recorrente -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Comissão Recorrente (%)
            </label>

            <input
                type="number"
                step="0.01"
                name="commission_recurring"
                value="{{ old('commission_recurring', 2) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('commission_recurring')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Meta Pacote Mensal -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Meta Pacote Mensal
            </label>

            <input
                type="number"
                name="monthly_package_target"
                value="{{ old('monthly_package_target') }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                required
            >
            @error('monthly_package_target')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <!-- Botões -->
    <div class="flex gap-2 mt-6">

        <button
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
        >
            Criar
        </button>

        <a
            href="{{ route('admin.sellers.index') }}"
            class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300"
        >
            Cancelar
        </a>

    </div>
</form>
@endsection
