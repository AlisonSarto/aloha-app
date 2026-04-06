@extends('layouts.admin')

@section('title', 'Metas do Vendedor')

@section('content')

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Metas — {{ $seller->user->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::create($year, $month)->translatedFormat('F \d\e Y') }}</p>
        </div>
        <a href="{{ route('admin.sellers.show', $seller) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
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

    {{-- Goals configuration form --}}
    @php
        $goalDefs = [
            'new_stores'    => ['label' => 'Novas lojas',      'icon' => 'fa-store'],
            'active_stores' => ['label' => 'Lojas ativas',     'icon' => 'fa-chart-line'],
            'packages'      => ['label' => 'Pacotes vendidos', 'icon' => 'fa-box'],
        ];
    @endphp

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">
            Configurar metas para {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
        </h2>

        <form method="POST" action="{{ route('admin.sellers.goals.update', $seller) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <input type="hidden" name="year"  value="{{ $year }}"/>
            <input type="hidden" name="month" value="{{ $month }}"/>

            @foreach($goalDefs as $key => $def)
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="{{ $key }}_enabled" value="1"
                            {{ $goal->{$key.'_enabled'} ? 'checked' : '' }}
                            class="h-4 w-4 rounded text-green-600"/>
                        <span class="text-sm font-medium text-gray-900">
                            <i class="fas {{ $def['icon'] }} text-green-600 mr-1"></i>{{ $def['label'] }}
                        </span>
                    </label>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Meta (quantidade)</label>
                    <input type="number" name="{{ $key }}_target" min="1"
                        value="{{ $goal->{$key.'_target'} }}"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 w-full sm:w-40"
                        placeholder="Ex: 10"/>
                </div>
            </div>
            @endforeach

            <button type="submit" class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition shadow-sm">
                Salvar metas
            </button>
        </form>
    </div>

@endsection
