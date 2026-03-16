@extends('layouts.admin')

@section('title', 'Visualizar Vendedor')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    {{ $seller->user->name }}
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Nome -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Nome</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->user->name }}
    </p>
</div>

<!-- Email -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Email</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->user->email }}
    </p>
</div>

<!-- Telefone -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Telefone</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->phone }}
    </p>
</div>

<!-- Comissão Novo Cliente -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Comissão Novo Cliente</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->commission_new_client }}%
    </p>
</div>

<!-- Comissão Recorrente -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Comissão Recorrente</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->commission_recurring }}%
    </p>
</div>

<!-- Meta Pacote Mensal -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Meta Pacote Mensal</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->monthly_package_target }}
    </p>
</div>

<!-- Data de criação -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Data de criação</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->created_at->format('d/m/Y H:i') }}
    </p>
</div>

<!-- Última atualização -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Última atualização</p>
    <p class="text-lg font-semibold mt-1">
        {{ $seller->updated_at->format('d/m/Y H:i') }}
    </p>
</div>

</div>

<!-- Botões -->

<div class="mt-8 flex gap-3">

<a href="{{ route('admin.sellers.index') }}"
   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
    Voltar
</a>

<a href="{{ route('admin.sellers.edit', $seller) }}"
   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
    Editar
</a>

<form method="POST" action="{{ route('admin.sellers.destroy', $seller) }}" style="display: inline;">
    @csrf
    @method('DELETE')
    <button type="submit"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition"
            onclick="return confirm('Tem certeza que deseja excluir este vendedor?')">
        Excluir
    </button>
</form>

</div>

@endsection