@extends('layouts.admin')

@section('title', 'Visualizar Usuário')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    {{ $user->name }}
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Nome -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Nome</p>
    <p class="text-lg font-semibold mt-1">
        {{ $user->name }}
    </p>
</div>

<!-- Email -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Email</p>
    <p class="text-lg font-semibold mt-1">
        {{ $user->email }}
    </p>
</div>

<!-- Data de criação -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Data de criação</p>
    <p class="text-lg font-semibold mt-1">
        {{ $user->created_at->format('d/m/Y H:i') }}
    </p>
</div>

<!-- Última atualização -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Última atualização</p>
    <p class="text-lg font-semibold mt-1">
        {{ $user->updated_at->format('d/m/Y H:i') }}
    </p>
</div>

</div>

<!-- Botões -->

<div class="mt-8 flex gap-3">

<a href="{{ route('admin.users.index') }}"
   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
    Voltar
</a>

<a href="{{ route('admin.users.edit', $user) }}"
   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
    Editar
</a>

@if($user->id !== auth()->id())
<form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
            onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
        Excluir
    </button>
</form>
@endif

</div>

@endsection
