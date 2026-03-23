@extends('layouts.client')

@section('title', 'Pedido #' . $order['codigo'])

@section('content')
    @php
        $situacao = mb_strtolower($order['nome_situacao'] ?? '');
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

        $isRetirada = str_contains($order['observacoes'] ?? '', 'RETIRADA NO LOCAL');

        $produtos = collect($order['produtos'] ?? [])->map(fn($p) => $p['produto']);
        $servicos = collect($order['servicos'] ?? [])->map(fn($s) => $s['servico']);
        $pagamentos = collect($order['pagamentos'] ?? [])->map(fn($p) => $p['pagamento']);

        $itemsSubtotal = $produtos->sum(fn($p) => (float) ($p['valor_total'] ?? 0))
                       + $servicos->sum(fn($s) => (float) ($s['valor_total'] ?? 0));

        $frete = (float) ($order['valor_frete'] ?? 0);
        $total = (float) ($order['valor_total'] ?? 0);

        $obs = trim($order['observacoes'] ?? '');
        $clientNote = str_contains($obs, '=== Observação do Cliente ===')
            ? trim(explode('=== Observação do Cliente ===', $obs)[1])
            : '';
    @endphp

    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('client.orders.index') }}"
           class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 transition hover:text-green-600">
            <i class="fas fa-arrow-left text-xs"></i> Meus Pedidos
        </a>

        <div class="mt-3 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pedido #{{ $order['codigo'] }}</h1>
                <p class="mt-0.5 text-sm text-gray-500">
                    Emitido em {{ $orderDate }}
                    @if ($deliveryDate) · Entrega {{ $deliveryDate }} @endif
                </p>
            </div>
            <span class="inline-flex flex-shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold {{ $statusConfig['class'] }}">
                <i class="fas {{ $statusConfig['icon'] }} text-xs"></i>
                {{ $statusConfig['label'] }}
            </span>
        </div>

        <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
            @if ($isRetirada)
                <i class="fas fa-store text-xs"></i> Retirada no local
            @else
                <i class="fas fa-truck text-xs"></i> Entrega
            @endif
        </div>
    </div>

    <div class="space-y-4">

        {{-- Itens --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Itens do pedido</p>
            </div>

            <div class="divide-y divide-gray-50">
                @foreach ($produtos as $produto)
                    @php
                        $qtd       = (float) ($produto['quantidade'] ?? 1);
                        $unitPrice = (float) ($produto['valor_venda'] ?? 0);
                        $subtotal  = (float) ($produto['valor_total'] ?? 0);
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">{{ $produto['nome_produto'] ?? 'Produto' }}</p>
                            <p class="text-xs text-gray-400">
                                {{ number_format($qtd, 0) }} × R$&nbsp;{{ number_format($unitPrice, 2, ',', '.') }}
                            </p>
                        </div>
                        <p class="flex-shrink-0 font-semibold text-gray-900">
                            R$&nbsp;{{ number_format($subtotal, 2, ',', '.') }}
                        </p>
                    </div>
                @endforeach

                @foreach ($servicos as $servico)
                    @php
                        $qtd       = (float) ($servico['quantidade'] ?? 1);
                        $unitPrice = (float) ($servico['valor_venda'] ?? 0);
                        $subtotal  = (float) ($servico['valor_total'] ?? 0);
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">{{ $servico['nome_servico'] ?? 'Serviço' }}</p>
                            <p class="text-xs text-gray-400">
                                {{ number_format($qtd, 0) }} × R$&nbsp;{{ number_format($unitPrice, 2, ',', '.') }}
                            </p>
                        </div>
                        <p class="flex-shrink-0 font-semibold text-gray-900">
                            R$&nbsp;{{ number_format($subtotal, 2, ',', '.') }}
                        </p>
                    </div>
                @endforeach

                @if ($produtos->isEmpty() && $servicos->isEmpty())
                    <div class="px-4 py-6 text-center text-sm text-gray-400">
                        Nenhum item registrado.
                    </div>
                @endif
            </div>
        </div>

        {{-- Totais --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Totais</p>
            </div>
            <div class="divide-y divide-gray-50 px-4">
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-500">Subtotal</span>
                    <span class="text-sm font-medium text-gray-900">
                        R$&nbsp;{{ number_format($itemsSubtotal, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm text-gray-500">Frete</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if ($frete > 0)
                            R$&nbsp;{{ number_format($frete, 2, ',', '.') }}
                        @else
                            <span class="text-green-600">Grátis</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center justify-between py-3">
                    <span class="font-semibold text-gray-900">Total</span>
                    <span class="text-lg font-bold text-gray-900">
                        R$&nbsp;{{ number_format($total, 2, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Pagamentos --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-black/5">
            <div class="border-b border-gray-100 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Pagamento</p>
            </div>

            <div class="divide-y divide-gray-50">
                @forelse ($pagamentos as $i => $pg)
                    @php
                        $venc = ($pg['data_vencimento'] ?? null)
                            ? \Carbon\Carbon::parse($pg['data_vencimento'])->format('d/m/Y')
                            : '–';
                    @endphp
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $pg['nome_forma_pagamento'] ?? '–' }}</p>
                            <p class="text-xs text-gray-400">
                                Parcela {{ $i + 1 }} · Venc. {{ $venc }}
                            </p>
                        </div>
                        <p class="font-semibold text-gray-900">
                            R$&nbsp;{{ number_format((float) ($pg['valor'] ?? 0), 2, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-sm text-gray-400">
                        Nenhum pagamento registrado.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Observações --}}
        @if ($clientNote)
            <div class="overflow-hidden rounded-xl bg-amber-50 ring-1 ring-amber-100">
                <div class="border-b border-amber-100 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Observações</p>
                </div>
                <p class="whitespace-pre-line px-4 py-3 text-sm text-amber-800">{{ $clientNote }}</p>
            </div>
        @endif

    </div>
@endsection
