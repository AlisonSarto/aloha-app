@extends('layouts.admin')

@section('title', 'Visualizar Comércio')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    {{ $store->name }}
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Frete -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Frete</p>
    <p class="text-lg font-semibold mt-1">
        R$ {{ number_format($store->shipping_amount, 2, ',', '.') }}
    </p>
</div>

<!-- Tabela de preço -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Tabela de preço</p>
    <a class="text-lg font-semibold mt-1 text-blue-600 hover:underline" href='../price-tables/{{ $store->priceTable->id }}'>
        {{ $store->priceTable->name ?? 'Tabela padrão' }}
    </a>
</div>

<!-- Pode usar boleto -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Aceita boleto</p>
    <p class="text-lg font-semibold mt-1">
        {{ $store->can_use_boleto ? 'Sim' : 'Não' }}
    </p>
</div>

<!-- Vencimento boleto -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Dias para vencimento do boleto</p>
    <p class="text-lg font-semibold mt-1">
        {{ $store->boleto_due_days }} dias
    </p>
</div>

<!-- Pedidos -->
<div class="bg-white border border-gray-200 rounded-xl p-5">
    <p class="text-sm text-gray-500">Total de pedidos</p>
    <p class="text-lg font-semibold mt-1">
        {{ $store->orders_count }}
    </p>
</div>

</div>

<!-- Botões -->

<div class="mt-8 flex gap-3">

<a href="/admin/stores"
   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
    Voltar
</a>

<a href="/admin/stores/{{ $store->id }}/edit"
   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
    Editar
</a>

</div>

@endsection
