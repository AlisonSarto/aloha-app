@extends('layouts.client')

@section('title', 'Meus Pedidos')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Meus Pedidos</h1>
        <a href="{{ route('client.orders.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
            <i class="fas fa-cart-plus"></i> Novo pedido
        </a>
    </div>

    @if (empty($orders))
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                <i class="fas fa-clock-rotate-left text-2xl text-gray-400"></i>
            </div>
            <p class="font-medium text-gray-500">Nenhum pedido ainda</p>
            <p class="mt-1 text-sm text-gray-400">Seus pedidos aparecerão aqui.</p>
            <a href="{{ route('client.orders.create') }}"
               class="mt-6 inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
                <i class="fas fa-cart-plus"></i> Fazer um pedido
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach ($orders as $order)
                @php
                    $situacao = mb_strtolower($order['nome_situacao']);
                    $statusConfig = match (true) {
                        str_contains($situacao, 'em rota') => [
                            'label' => 'Em rota',
                            'class' => 'bg-blue-100 text-blue-700',
                            'icon'  => 'fa-truck'
                        ],

                        str_contains($situacao, 'preparando envio') => [
                            'label' => 'Preparando envio',
                            'class' => 'bg-orange-100 text-orange-700',
                            'icon'  => 'fa-box'
                        ],

                        str_contains($situacao, 'concluído') => [
                            'label' => $order['nome_situacao'],
                            'class' => 'bg-green-100 text-green-700',
                            'icon'  => 'fa-box-open'
                        ],

                        str_contains($situacao, 'analise') => [
                            'label' => 'Em análise',
                            'class' => 'bg-gray-100 text-gray-700',
                            'icon'  => 'fa-clock'
                        ],

                        default => [
                            'label' => 'Em análise',
                            'class' => 'bg-gray-100 text-gray-700',
                            'icon'  => 'fa-clock'
                        ],
                    };

                    $orderDate    = ($order['data'] ?? null)             ? \Carbon\Carbon::parse($order['data'])->format('d/m/Y')             : '–';
                    $deliveryDate = ($order['previsao_entrega'] ?? null) ? \Carbon\Carbon::parse($order['previsao_entrega'])->format('d/m/Y') : null;

                    $pagamento    = $order['pagamentos'][0]['pagamento'] ?? null;
                    $paymentLabel = $pagamento['nome_forma_pagamento'] ?? '–';

                    $isRetirada = str_contains($order['observacoes'] ?? '', 'RETIRADA');

                    $allItems = collect($order['produtos'] ?? [])
                        ->map(fn($p) => ['nome' => $p['produto']['nome_produto'] ?? 'Produto', 'qtd' => $p['produto']['quantidade'] ?? 1])
                        ->merge(
                            collect($order['servicos'] ?? [])
                                ->map(fn($s) => ['nome' => $s['servico']['nome_servico'] ?? 'Serviço', 'qtd' => $s['servico']['quantidade'] ?? 1])
                        );
                @endphp

                <a href="{{ route('client.orders.show', $order['id']) }}"
                    class="block rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5 transition hover:ring-2 hover:ring-green-400">

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900">Pedido #{{ $order['codigo'] }}</p>
                            <p class="mt-0.5 text-xs text-gray-400">
                                {{ $orderDate }}
                                @if ($deliveryDate) · Entrega {{ $deliveryDate }} @endif
                                · {{ $isRetirada ? 'Retirada' : 'Entrega' }}
                            </p>
                        </div>
                        <span class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusConfig['class'] }}">
                            <i class="fas {{ $statusConfig['icon'] }} text-xs"></i>
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    @if ($allItems->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-1.5 border-t border-gray-50 pt-3">
                            @foreach ($allItems->take(4) as $item)
                                <span class="inline-flex items-center gap-1 rounded-lg bg-gray-50 px-2 py-1 text-xs text-gray-600">
                                    <span class="font-medium">{{ number_format((float) $item['qtd'], 0) }}×</span>
                                    {{ $item['nome'] }}
                                </span>
                            @endforeach
                            @if ($allItems->count() > 4)
                                <span class="inline-flex items-center rounded-lg bg-gray-100 px-2 py-1 text-xs text-gray-400">
                                    +{{ $allItems->count() - 4 }} itens
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-3 flex items-center justify-between border-t border-gray-50 pt-3">
                        <span class="text-xs text-gray-400">{{ $paymentLabel }}</span>
                        <span class="text-sm font-bold text-gray-900">
                            R$&nbsp;{{ number_format((float) $order['valor_total'], 2, ',', '.') }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($prevPage || $nextPage)
            <nav class="mt-6 flex items-center justify-between">
                @if ($prevPage)
                    <a href="{{ route('client.orders.index', ['page' => $prevPage]) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm transition hover:border-green-400 hover:text-green-700">
                        <i class="fas fa-chevron-left text-xs"></i> Anterior
                    </a>
                @else
                    <span></span>
                @endif

                <span class="text-sm text-gray-400">Página {{ $currentPage }}</span>

                @if ($nextPage)
                    <a href="{{ route('client.orders.index', ['page' => $nextPage]) }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm transition hover:border-green-400 hover:text-green-700">
                        Próxima <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                @else
                    <span></span>
                @endif
            </nav>
        @endif
    @endif
@endsection
