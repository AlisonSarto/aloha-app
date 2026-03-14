@extends('layouts.admin')

@section('title', 'Editar Usuário')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Editar Usuário
</h1>

<form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')

    <div class="bg-white border border-gray-300 rounded-lg p-6 space-y-6">

        <!-- Nome -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nome
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $user->name) }}"
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
                value="{{ old('email', $user->email) }}"
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
                Nova Senha (deixe em branco para manter a atual)
            </label>

            <input
                type="password"
                name="password"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirmar Senha -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Confirmar Nova Senha
            </label>

            <input
                type="password"
                name="password_confirmation"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
        </div>

    </div>

    <!-- Botões -->
    <div class="flex gap-2 mt-6">

        <button
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
        >
            Salvar
        </button>

        <a
            href="{{ route('admin.users.show', $user) }}"
            class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300"
        >
            Cancelar
        </a>

    </div>

</form>

@endsection
