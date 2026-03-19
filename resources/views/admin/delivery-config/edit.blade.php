@extends('layouts.admin')

@section('title', 'Configurações de Entrega')

@section('content')
    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Configurações de Entrega</h1>
            <p class="text-sm text-gray-500 mt-1">Defina os dias disponíveis, prazo e comportamento de datas de entrega.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.delivery-config.update') }}">
            @csrf
            @method('PUT')

            {{-- Dias de entrega --}}
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 p-6 mb-4">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Dias com entrega disponível</h2>
                <p class="text-xs text-gray-500 mb-4">Marque os dias da semana em que a empresa realiza entregas.</p>

                @php
                    $days = [
                        0 => 'Segunda-feira',
                        1 => 'Terça-feira',
                        2 => 'Quarta-feira',
                        3 => 'Quinta-feira',
                        4 => 'Sexta-feira',
                        5 => 'Sábado',
                        6 => 'Domingo',
                    ];
                @endphp

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($days as $value => $label)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   name="delivery_days[]"
                                   value="{{ $value }}"
                                   {{ in_array($value, $config->delivery_days ?? []) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                @error('delivery_days')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prazo de entrega --}}
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 p-6 mb-4">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Prazo de entrega (dias)</h2>
                <p class="text-xs text-gray-500 mb-4">
                    Quantos dias corridos de antecedência o pedido precisa ser feito.
                    Ex: <strong>1</strong> significa que o pedido será entregue no mínimo no dia seguinte ao pedido.
                </p>

                <input type="number"
                       name="lead_days"
                       value="{{ old('lead_days', $config->lead_days) }}"
                       min="0" max="30"
                       class="w-24 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">

                <span class="ml-2 text-sm text-gray-500">dia(s)</span>

                @error('lead_days')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Comportamento de dia indisponível --}}
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 p-6 mb-6">
                <h2 class="text-base font-semibold text-gray-900 mb-1">Quando o prazo cair em dia sem entrega</h2>
                <p class="text-xs text-gray-500 mb-4">
                    Se a data calculada pelo prazo não tiver entrega disponível, o sistema deve escolher:
                </p>

                <div class="space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio"
                               name="late_direction"
                               value="after"
                               {{ old('late_direction', $config->late_direction) === 'after' ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 border-gray-300 text-green-600 focus:ring-green-500">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Próximo dia disponível</p>
                            <p class="text-xs text-gray-500">O pedido será agendado para o próximo dia em que há entrega após o prazo.</p>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio"
                               name="late_direction"
                               value="before"
                               {{ old('late_direction', $config->late_direction) === 'before' ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 border-gray-300 text-green-600 focus:ring-green-500">
                        <div>
                            <p class="text-sm font-medium text-gray-800">Dia disponível anterior</p>
                            <p class="text-xs text-gray-500">O pedido será agendado para o último dia com entrega antes do prazo.</p>
                        </div>
                    </label>
                </div>

                @error('late_direction')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="rounded-lg bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                    <i class="fas fa-floppy-disk mr-1.5"></i> Salvar configurações
                </button>
            </div>
        </form>
    </div>
@endsection
