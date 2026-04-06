@extends('layouts.seller')

@section('title', 'Dashboard')

@section('content')

    {{-- Welcome banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-green-600 to-green-700 p-6 shadow-lg mb-6 mt-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-green-100 text-xs font-semibold uppercase tracking-widest mb-1">Área do Vendedor</p>
                <p class="text-white text-xl font-bold">Olá, {{ auth()->user()->name }}!</p>
                <p class="text-green-200 text-sm mt-1">
                    {{ now()->format('F Y') }} — acompanhe suas metas e comissões.
                </p>
            </div>
            <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-white/20 items-center justify-center flex-shrink-0">
                <i class="fas fa-handshake text-white text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Comissão do mês</p>
            <p class="text-xl font-bold text-green-700">R$ {{ number_format($kpis['commission_total'], 2, ',', '.') }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Novas lojas</p>
            <p class="text-xl font-bold text-gray-900">{{ $kpis['new_stores_count'] }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Lojas ativas</p>
            <p class="text-xl font-bold text-gray-900">{{ $kpis['active_stores_count'] }}</p>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4">
            <p class="text-xs text-gray-500 mb-1">Pacotes vendidos</p>
            <p class="text-xl font-bold text-gray-900">{{ $kpis['packages_count'] }}</p>
        </div>

    </div>

    {{-- Goals progress --}}
    @php $gp = $kpis['goal_progress']; @endphp
    @if(($gp['new_stores']['enabled'] ?? false) || ($gp['active_stores']['enabled'] ?? false) || ($gp['packages']['enabled'] ?? false))
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-900">Metas do mês</h2>
            <a href="{{ route('seller.reports.goals') }}" class="text-xs text-green-600 hover:underline">Ver detalhes</a>
        </div>

        <div class="space-y-4">
            @foreach([
                ['key' => 'new_stores',    'label' => 'Novas lojas'],
                ['key' => 'active_stores', 'label' => 'Lojas ativas'],
                ['key' => 'packages',      'label' => 'Pacotes'],
            ] as $g)
                @if($gp[$g['key']]['enabled'] ?? false)
                    @php $gi = $gp[$g['key']]; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="text-gray-600">{{ $g['label'] }}</span>
                            <span class="font-semibold {{ $gi['pct'] >= 100 ? 'text-green-700' : 'text-gray-700' }}">
                                {{ $gi['actual'] }} / {{ $gi['target'] }} ({{ $gi['pct'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $gi['pct'] >= 100 ? 'bg-green-600' : ($gi['on_track'] ? 'bg-blue-500' : 'bg-amber-500') }}"
                                style="width: {{ min(100, $gi['pct']) }}%"></div>
                        </div>
                        @if($gi['remaining'] > 0 && $gi['days_to_hit'])
                            <p class="text-xs text-gray-400 mt-1">Faltam {{ $gi['remaining'] }} — previsão: {{ $gi['days_to_hit'] }} dias</p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick access --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('seller.stores.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-store text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Minhas Lojas</p>
                <p class="text-xs text-gray-400 mt-0.5">Cadastrar e gerenciar</p>
            </div>
        </a>
        <a href="{{ route('seller.reports.commissions') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-coins text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Comissões</p>
                <p class="text-xs text-gray-400 mt-0.5">Por período e loja</p>
            </div>
        </a>
        <a href="{{ route('seller.reports.stores') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-chart-bar text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Relatório de Lojas</p>
                <p class="text-xs text-gray-400 mt-0.5">Desempenho por loja</p>
            </div>
        </a>
        <a href="{{ route('seller.reports.goals') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-bullseye text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Metas</p>
                <p class="text-xs text-gray-400 mt-0.5">Progresso mensal</p>
            </div>
        </a>
    </div>

    {{-- Recent stores --}}
    @if($recentStores->isNotEmpty())
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-900">Lojas recentes</h2>
            <a href="{{ route('seller.stores.index') }}" class="text-xs text-green-600 hover:underline">Ver todas</a>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentStores as $store)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $store->name }}</p>
                        <p class="text-xs text-gray-400">{{ $store->address_city }}/{{ $store->address_state }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $store->seller_assignment_status === 'approved' ? 'bg-green-100 text-green-700' :
                           ($store->seller_assignment_status === 'pending'  ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                        {{ match($store->seller_assignment_status) {
                            'approved' => 'Aprovada',
                            'pending'  => 'Pendente',
                            'rejected' => 'Rejeitada',
                            default    => '—'
                        } }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Pending claims --}}
    @if($pendingClaims->isNotEmpty())
    <div class="rounded-xl bg-amber-50 border border-amber-200 p-5">
        <h2 class="text-sm font-semibold text-amber-800 mb-3">Solicitações pendentes de aprovação ({{ $pendingClaims->count() }})</h2>
        <div class="space-y-2">
            @foreach($pendingClaims as $claim)
                <p class="text-sm text-amber-700">
                    <i class="fas fa-clock text-xs mr-1"></i>
                    {{ $claim->store->name }} — solicitado em {{ $claim->created_at->format('d/m/Y') }}
                </p>
            @endforeach
        </div>
    </div>
    @endif

@endsection
