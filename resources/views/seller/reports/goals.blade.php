@extends('layouts.seller')

@section('title', 'Metas')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Metas do Mês</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::create($year, $month)->translatedFormat('F \d\e Y') }}</p>
        </div>
    </div>

    {{-- Month selector --}}
    <form method="GET" class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Mês</label>
            <input type="month" name="month_year"
                value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}"
                onchange="this.form.year.value=this.value.split('-')[0]; this.form.month.value=parseInt(this.value.split('-')[1])"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"/>
            <input type="hidden" name="year" value="{{ $year }}"/>
            <input type="hidden" name="month" value="{{ $month }}"/>
        </div>
        <button type="submit" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition shadow-sm">
            Ver mês
        </button>
    </form>

    {{-- Progress cards --}}
    @php
        $goalDefs = [
            'new_stores'    => ['label' => 'Novas lojas',   'icon' => 'fa-store',   'actual' => $kpis['new_stores_count']],
            'active_stores' => ['label' => 'Lojas ativas',  'icon' => 'fa-chart-line', 'actual' => $kpis['active_stores_count']],
            'packages'      => ['label' => 'Pacotes vendidos', 'icon' => 'fa-box', 'actual' => $kpis['packages_count']],
        ];
        $anyEnabled = collect($progress)->contains(fn($g) => $g['enabled'] ?? false);
    @endphp

    @if(!$anyEnabled)
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-6 text-center mb-6">
            <i class="fas fa-bullseye text-amber-400 text-3xl mb-3"></i>
            <p class="text-amber-800 font-medium">Nenhuma meta ativada para este mês.</p>
            <p class="text-amber-600 text-sm mt-1">Entre em contato com o administrador para configurar suas metas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @foreach($goalDefs as $key => $def)
                @php $g = $progress[$key]; @endphp
                @if($g['enabled'] ?? false)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $def['icon'] }} text-green-700 text-sm"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ $def['label'] }}</p>
                    </div>
                    <div class="flex items-end justify-between mb-2">
                        <span class="text-2xl font-bold {{ $g['pct'] >= 100 ? 'text-green-700' : 'text-gray-900' }}">{{ $g['actual'] }}</span>
                        <span class="text-sm text-gray-400">/ {{ $g['target'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2">
                        <div class="h-2.5 rounded-full {{ $g['pct'] >= 100 ? 'bg-green-600' : ($g['on_track'] ? 'bg-blue-500' : 'bg-amber-500') }}"
                            style="width: {{ min(100, $g['pct']) }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="{{ $g['pct'] >= 100 ? 'text-green-700 font-semibold' : 'text-gray-500' }}">{{ $g['pct'] }}% atingido</span>
                        @if($g['pct'] < 100)
                            <span class="{{ $g['on_track'] ? 'text-blue-600' : 'text-amber-600' }}">
                                @if($g['on_track']) No ritmo!
                                @elseif($g['days_to_hit']) ~{{ $g['days_to_hit'] }} dias para bater
                                @else Ritmo insuficiente
                                @endif
                            </span>
                        @else
                            <span class="text-green-700 font-semibold"><i class="fas fa-check mr-1"></i>Meta batida!</span>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Info --}}
    @if($anyEnabled)
    <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-sm text-blue-700">
        <i class="fas fa-info-circle mr-1"></i>
        As metas são definidas pelo administrador. Entre em contato caso precise ajustá-las.
    </div>
    @endif

@endsection
