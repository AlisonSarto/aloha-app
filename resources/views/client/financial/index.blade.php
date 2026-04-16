@extends('layouts.client')

@section('title', 'Financeiro')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="mb-4 text-2xl font-bold text-gray-900">Financeiro</h1>
        <p class="text-sm text-gray-500 mt-1">
            <i class="fas fa-store mr-1"></i> {{ $store->name }}
            — apenas boletos bancários são exibidos aqui.
        </p>
    </div>

    {{-- Block alert (redirected from orders.create) --}}
    @if (session('overdue_block'))
        <div class="mb-5 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 p-4">
            <i class="fas fa-ban text-red-500 mt-0.5 text-lg shrink-0"></i>
            <p class="text-sm text-red-700 font-medium">{{ session('overdue_block') }}</p>
        </div>
    @endif

    {{-- Overdue boletos --}}
    @if (count($atrasados) > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                    <i class="fas fa-circle-exclamation"></i>
                    Em Atraso ({{ count($atrasados) }})
                </span>
            </div>

            <div class="flex flex-col gap-3">
                @foreach ($atrasados as $boleto)
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-red-800">
                                    Venda nº {{ $boleto['codigo'] ?? '—' }}
                                </p>
                                <p class="text-xs text-red-600 mt-0.5">
                                    Vencimento:
                                    @if ($boleto['data_vencimento'])
                                        {{ \Carbon\Carbon::parse($boleto['data_vencimento'])->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-bold text-red-700 shrink-0">
                                R$ {{ number_format($boleto['valor'], 2, ',', '.') }}
                            </span>
                        </div>
                        @forelse ($boleto['boleto_links'] as $i => $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition w-full">
                                <i class="fas fa-barcode"></i>
                                @if(count($boleto['boleto_links']) > 1)
                                    Pagar boleto {{ $i + 1 }}
                                    @if($link['data_vencimento']) — venc. {{ \Carbon\Carbon::parse($link['data_vencimento'])->format('d/m/Y') }}@endif
                                @else
                                    Pagar boleto
                                @endif
                            </a>
                        @empty
                            <p class="text-xs text-red-400 italic">Link do boleto indisponível no momento.</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Open boletos --}}
    @if (count($emAberto) > 0)
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
                <span
                    class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                    <i class="fas fa-clock"></i>
                    Em Aberto ({{ count($emAberto) }})
                </span>
            </div>

            <div class="flex flex-col gap-3">
                @foreach ($emAberto as $boleto)
                    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-semibold text-yellow-800">
                                    Venda nº {{ $boleto['codigo'] ?? '—' }}
                                </p>
                                <p class="text-xs text-yellow-600 mt-0.5">
                                    Vencimento:
                                    @if ($boleto['data_vencimento'])
                                        {{ \Carbon\Carbon::parse($boleto['data_vencimento'])->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-bold text-yellow-700 shrink-0">
                                R$ {{ number_format($boleto['valor'], 2, ',', '.') }}
                            </span>
                        </div>
                        @forelse ($boleto['boleto_links'] as $i => $link)
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-yellow-500 px-4 py-2 text-xs font-semibold text-white hover:bg-yellow-600 transition w-full">
                                <i class="fas fa-barcode"></i>
                                @if(count($boleto['boleto_links']) > 1)
                                    Ver boleto {{ $i + 1 }}
                                    @if($link['data_vencimento']) — venc. {{ \Carbon\Carbon::parse($link['data_vencimento'])->format('d/m/Y') }}@endif
                                @else
                                    Ver boleto
                                @endif
                            </a>
                        @empty
                            <p class="text-xs text-yellow-500 italic">Link do boleto indisponível no momento.</p>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty state --}}
    @if (count($atrasados) === 0 && count($emAberto) === 0)
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-green-100 flex items-center justify-center mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <p class="font-semibold text-gray-800">Nenhum boleto em aberto</p>
            <p class="text-sm text-gray-500 mt-1">Você está em dia!</p>
            <a href="{{ route('client.orders.create') }}"
                class="mt-6 inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">
                <i class="fas fa-cart-plus"></i> Fazer um pedido
            </a>
        </div>
    @endif

    {{-- All good, show CTA --}}
    @if (count($atrasados) === 0 && count($emAberto) > 0)
        <div class="mt-2 text-center">
            <a href="{{ route('client.orders.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">
                <i class="fas fa-cart-plus"></i> Fazer um pedido
            </a>
        </div>
    @endif

@endsection
