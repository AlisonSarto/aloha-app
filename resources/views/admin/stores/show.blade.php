@extends('layouts.admin')

@section('title', 'Visualizar Comércio')

@section('content')

@php
    $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    $hours = $store->storeHours->keyBy('day_of_week');
@endphp

@if (session('success'))
<div class="mb-4 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
</div>
@endif

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-bold">{{ $store->name }}</h1>
    <div class="flex gap-2">
        <a href="/admin/stores"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
        <a href="/admin/stores/{{ $store->id }}/edit"
           class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
            <i class="fas fa-pen-to-square text-xs"></i> Editar
        </a>
    </div>
</div>

<div class="space-y-5">

    {{-- Identificação --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-4 font-semibold text-gray-900">Identificação</h2>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-store text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Nome Fantasia</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->name ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-building text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Razão Social</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->legal_name ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-id-card text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">CNPJ</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->cnpj ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-link text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">ID Gestão Click</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->gestao_click_id ?: '—' }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Configurações Comerciais --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-4 font-semibold text-gray-900">Configurações Comerciais</h2>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-truck text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Frete</p>
                    <p class="text-sm font-medium text-gray-900">R$ {{ number_format($store->shipping_amount, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-table text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Tabela de Preço</p>
                    <a class="text-sm font-medium text-blue-600 hover:underline" href="../price-tables/{{ $store->priceTable->id }}">
                        {{ $store->priceTable->name ?? 'Tabela padrão' }}
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-file-invoice-dollar text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Aceita Boleto</p>
                    @if ($store->can_use_boleto)
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                            <i class="fas fa-check text-xs"></i> Sim
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                            Não
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-calendar-days text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Dias para Vencimento do Boleto</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->boleto_due_days }} dias</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-box text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Total de Pedidos</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->orders_count }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Endereço --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-4 font-semibold text-gray-900">Endereço</h2>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-map-pin text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">CEP</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_cep ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-road text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Logradouro</p>
                    <p class="text-sm font-medium text-gray-900">
                        @if ($store->address_street)
                            {{ $store->address_street }}{{ $store->address_number ? ', ' . $store->address_number : '' }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>

            @if ($store->address_complement)
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-info text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Complemento</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_complement }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-location-dot text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Bairro</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_district ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-city text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Cidade / Estado</p>
                    <p class="text-sm font-medium text-gray-900">
                        @if ($store->address_city)
                            {{ $store->address_city }}{{ $store->address_state ? ' — ' . $store->address_state : '' }}
                        @else
                            —
                        @endif
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Horário de Funcionamento --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-4 font-semibold text-gray-900">Horário de Funcionamento</h2>

        @if ($hours->isEmpty())
            <p class="text-sm text-gray-500">Horários não cadastrados.</p>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($days as $index => $dayName)
                    @php $hour = $hours->get($index); @endphp
                    <div class="flex items-center justify-between py-2.5">
                        <span class="w-8 text-sm font-medium text-gray-700">{{ $dayName }}</span>
                        @if ($hour && $hour->is_open)
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                <i class="fas fa-check text-xs"></i> Aberto
                            </span>
                            <span class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($hour->open_time)->format('H:i') }}
                                às
                                {{ \Carbon\Carbon::parse($hour->close_time)->format('H:i') }}
                            </span>
                        @elseif ($hour)
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                Fechado
                            </span>
                            <span class="text-sm text-gray-400">—</span>
                        @else
                            <span class="text-xs text-gray-400">Não configurado</span>
                            <span></span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
