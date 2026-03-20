@extends('layouts.client')

@section('title', 'Meus Pedidos')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Meus Pedidos</h1>
        <a href="{{ route('client.orders.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
            <i class="fas fa-cart-plus"></i> Novo pedido
        </a>
    </div>

    @if (empty($orders))
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
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                @php
                    $statusConfig = match ($order['status']) {
                        'confirmado' => ['label' => 'Confirmado', 'class' => 'bg-blue-100 text-blue-700'],
                        'entregue'   => ['label' => 'Entregue',   'class' => 'bg-green-100 text-green-700'],
                        'cancelado'  => ['label' => 'Cancelado',  'class' => 'bg-red-100 text-red-700'],
                        default      => ['label' => 'Aguardando', 'class' => 'bg-yellow-100 text-yellow-700'],
                    };

                    $paymentLabel = match ($order['forma_pagamento']) {
                        'boleto'   => 'Boleto',
                        'dinheiro' => 'Dinheiro',
                        'cartao'   => 'Cartão',
                        default    => 'Pix',
                    };

                    $deliveryLabel = ($order['tipo_entrega'] ?? 'entrega') === 'retirada'
                        ? '🏪 Retirada'
                        : '🚚 Entrega';

                    $deliveryDate = \Carbon\Carbon::parse($order['data_entrega'])->format('d/m/Y');
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900">{{ $order['numero'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $deliveryLabel }} · {{ $deliveryDate }} · {{ $paymentLabel }}
                            </p>
                        </div>
                        <span class="flex-shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusConfig['class'] }}">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    @if (!empty($order['itens']))
                        <div class="mt-3 pt-3 border-t border-gray-50 flex flex-wrap gap-1.5">
                            @foreach ($order['itens'] as $item)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-gray-50 px-2 py-1 text-xs text-gray-600">
                                    <span class="font-medium">{{ $item['quantidade'] }}×</span>
                                    {{ $item['nome'] }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between">
                        <span class="text-xs text-gray-400">Total</span>
                        <span class="text-sm font-bold text-gray-900">
                            R$&nbsp;{{ number_format($order['valor_total'], 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
