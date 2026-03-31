@extends('layouts.admin')

@section('title', 'Visualizar Comércio')

@section('content')

@php
    $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    $hours = $store->storeHours->keyBy('day_of_week');
@endphp

@if (session('success'))
    <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Page header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

    {{-- Left side --}}
    <div class="flex items-center gap-3 min-w-0">
        <div class="flex h-10 w-10 sm:h-11 sm:w-11 flex-shrink-0 items-center justify-center rounded-xl bg-green-100">
            <i class="fas fa-store text-green-700"></i>
        </div>

        <div class="min-w-0">
            <h1 class="text-lg sm:text-2xl font-bold text-gray-900 truncate">
                {{ $store->name }}
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                {{ $store->cnpj ?: 'Comércio' }}
            </p>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">

        <a href="/admin/stores"
           class="w-full sm:w-auto justify-center inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>

        <a href="/admin/stores/{{ $store->id }}/edit"
           class="w-full sm:w-auto justify-center inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
            <i class="fas fa-pen-to-square text-xs"></i> Editar
        </a>

    </div>
</div>

<div class="space-y-4">

    {{-- Identificação --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-building text-green-600 text-sm"></i>
            <h2 class="text-sm font-semibold text-gray-900">Identificação</h2>
        </div>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-store text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Nome Fantasia</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->name ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-building text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Razão Social</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->legal_name ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-id-card text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">CNPJ</p>
                    <p class="text-sm font-medium text-gray-900 font-mono">{{ $store->cnpj ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-link text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">ID Gestão Click</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->gestao_click_id ?: '—' }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Configurações Comerciais --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-gear text-green-600 text-sm"></i>
            <h2 class="text-sm font-semibold text-gray-900">Configurações Comerciais</h2>
        </div>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-truck text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Frete</p>
                    <p class="text-sm font-medium text-gray-900">R$ {{ number_format($store->shipping_amount, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-table text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Tabela de Preço</p>
                    <a class="text-sm font-medium text-green-600 hover:underline" href="../price-tables/{{ $store->priceTable->id }}">
                        {{ $store->priceTable->name ?? 'Tabela padrão' }}
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-file-invoice-dollar text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Aceita Boleto</p>
                    @if ($store->can_use_boleto)
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                            <i class="fas fa-check text-xs"></i> Sim — vence em {{ $store->boleto_due_days }} dias
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
                    <i class="fas fa-box text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Total de Pedidos</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->orders_count }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Endereço --}}
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-map-location-dot text-green-600 text-sm"></i>
            <h2 class="text-sm font-semibold text-gray-900">Endereço</h2>
        </div>
        <div class="space-y-3">

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-map-pin text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">CEP</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_cep ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-road text-xs"></i>
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
                    <i class="fas fa-info text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Complemento</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_complement }}</p>
                </div>
            </div>
            @endif

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-location-dot text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Bairro</p>
                    <p class="text-sm font-medium text-gray-900">{{ $store->address_district ?: '—' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-city text-xs"></i>
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
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-clock text-green-600 text-sm"></i>
            <h2 class="text-sm font-semibold text-gray-900">Horário de Funcionamento</h2>
        </div>

        @if ($hours->isEmpty())
            <p class="text-sm text-gray-500">Horários não cadastrados.</p>
        @else
            <div class="divide-y divide-gray-100">
                @foreach ($days as $index => $dayName)
                    @php $hour = $hours->get($index); @endphp
                    <div class="flex items-center justify-between py-2.5">
                        <span class="w-8 text-xs font-bold text-gray-500">{{ $dayName }}</span>
                        @if ($hour && $hour->is_open)
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                <i class="fas fa-check text-xs"></i> Aberto
                            </span>
                            <span class="text-sm text-gray-700 font-medium">
                                {{ \Carbon\Carbon::parse($hour->open_time)->format('H:i') }}
                                <span class="text-gray-400 font-normal">às</span>
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
