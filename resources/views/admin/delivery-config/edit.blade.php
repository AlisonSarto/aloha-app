@extends('layouts.admin')

@section('title', 'Configurações de Entrega')

@section('content')

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Configurações de Entrega</h1>
            <p class="text-sm text-gray-500 mt-0.5">Defina os dias disponíveis, prazo e comportamento de datas de entrega.</p>
        </div>

        @if (session('success'))
            <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.delivery-config.update') }}">
            @csrf
            @method('PUT')

            {{-- Dias de entrega --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-calendar-days text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Dias com entrega disponível</h2>
                </div>
                <p class="text-xs text-gray-500 mb-4 ml-5">Selecione os dias da semana em que a empresa realiza entregas.</p>

                @php
                    $days = [
                        0 => 'Seg',
                        1 => 'Ter',
                        2 => 'Qua',
                        3 => 'Qui',
                        4 => 'Sex',
                        5 => 'Sáb',
                        6 => 'Dom',
                    ];
                @endphp

                <div class="flex flex-wrap gap-2">
                    @foreach ($days as $value => $label)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="delivery_days[]" value="{{ $value }}"
                                {{ in_array($value, $config->delivery_days ?? []) ? 'checked' : '' }}
                                class="peer sr-only">
                            <span class="flex items-center justify-center w-12 h-12 rounded-xl text-sm font-bold ring-1 transition select-none
                                bg-gray-50 text-gray-400 ring-gray-200 hover:ring-green-300
                                peer-checked:bg-green-600 peer-checked:text-white peer-checked:ring-green-600">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('delivery_days')
                    <p class="mt-3 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prazo de entrega --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-clock text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Prazo de antecedência</h2>
                </div>
                <p class="text-xs text-gray-500 mb-4 ml-5">
                    Quantos dias corridos o pedido precisa ser feito antes da entrega.
                    Ex: <strong>1</strong> = entrega no mínimo no dia seguinte ao pedido.
                </p>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="stepLeadDays(-1)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <input type="number" id="lead_days" name="lead_days"
                        value="{{ old('lead_days', $config->lead_days) }}"
                        min="0" max="30"
                        class="w-20 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-center text-sm font-bold text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    <button type="button" onclick="stepLeadDays(1)"
                        class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                    <span class="text-sm text-gray-500">dia(s)</span>
                </div>

                @error('lead_days')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Comportamento de dia indisponível --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 mb-6">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fas fa-route text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Quando o prazo cair em dia sem entrega</h2>
                </div>
                <p class="text-xs text-gray-500 mb-4 ml-5">
                    Se a data calculada não tiver entrega disponível, agendar para:
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="late_direction" value="after"
                            {{ old('late_direction', $config->late_direction) === 'after' ? 'checked' : '' }}
                            class="peer sr-only">
                        <div class="rounded-xl p-4 ring-1 ring-gray-200 bg-gray-50 transition-all
                            peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-7 h-7 rounded-lg bg-white ring-1 ring-black/5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-forward-step text-green-600 text-xs"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">Próximo disponível</span>
                            </div>
                            <p class="text-xs text-gray-500 ml-9">O próximo dia com entrega após o prazo.</p>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="late_direction" value="before"
                            {{ old('late_direction', $config->late_direction) === 'before' ? 'checked' : '' }}
                            class="peer sr-only">
                        <div class="rounded-xl p-4 ring-1 ring-gray-200 bg-gray-50 transition-all
                            peer-checked:ring-2 peer-checked:ring-green-500 peer-checked:bg-green-50">
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-7 h-7 rounded-lg bg-white ring-1 ring-black/5 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-backward-step text-green-600 text-xs"></i>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">Anterior disponível</span>
                            </div>
                            <p class="text-xs text-gray-500 ml-9">O último dia com entrega antes do prazo.</p>
                        </div>
                    </label>
                </div>

                @error('late_direction')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                    <i class="fas fa-floppy-disk text-xs"></i> Salvar Configurações
                </button>
            </div>
        </form>
    </div>

    <script>
        function stepLeadDays(delta) {
            const input = document.getElementById('lead_days');
            const next = Math.max(0, Math.min(30, (parseInt(input.value) || 0) + delta));
            input.value = next;
        }
    </script>

@endsection
