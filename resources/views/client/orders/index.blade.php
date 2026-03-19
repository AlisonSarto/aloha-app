@extends('layouts.client')

@section('title', 'Meus Pedidos')

@section('content')
    <h1 class="mb-4 text-2xl font-bold text-gray-900">Meus Pedidos</h1>

    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
            <i class="fas fa-clock-rotate-left text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-500 font-medium">Nenhum pedido ainda</p>
        <p class="text-sm text-gray-400 mt-1">Seus pedidos aparecerão aqui.</p>
        <a href="{{ route('client.orders.create') }}"
           class="mt-6 inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
            <i class="fas fa-cart-plus"></i> Fazer um pedido
        </a>
    </div>
@endsection
