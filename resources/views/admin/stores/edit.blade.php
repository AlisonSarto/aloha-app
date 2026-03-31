@extends('layouts.admin')

@section('title', 'Editar Comércio')

@section('content')

@php
    $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    $hours = $store->storeHours->keyBy('day_of_week');
@endphp

<div class="max-w-2xl">

    {{-- Page header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="/admin/stores/{{ $store->id }}"
            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-500 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Comércio</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $store->name }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
            <i class="fas fa-circle-exclamation mr-1.5"></i> Corrija os erros abaixo antes de salvar.
        </div>
    @endif

    {{-- Identificação (somente leitura) --}}
    <div class="mb-4 rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-building text-green-600 text-sm"></i>
            <h2 class="text-sm font-semibold text-gray-900">Identificação</h2>
            <span class="ml-auto text-xs text-gray-400">somente leitura</span>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">CNPJ</label>
                <input type="text" value="{{ $store->cnpj }}" disabled
                    class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Razão Social</label>
                <input type="text" value="{{ $store->legal_name }}" disabled
                    class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">
            </div>
        </div>
    </div>

    <form method="POST" action="/admin/stores/{{ $store->id }}">
        @csrf
        @method('PUT')

        <div class="space-y-4">

            {{-- Configurações Comerciais --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 space-y-5">
                <div class="flex items-center gap-2">
                    <i class="fas fa-gear text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Configurações Comerciais</h2>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Nome do Comércio</label>
                    <input type="text" name="name" value="{{ old('name', $store->name) }}"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Valor do Frete</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none">R$</span>
                        <input type="number" step="0.01" name="shipping_amount"
                            value="{{ old('shipping_amount', $store->shipping_amount) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white pl-10 pr-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tabela de Preço</label>
                    <select name="price_table_id"
                        class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @foreach($priceTables as $table)
                            <option value="{{ $table->id }}" {{ $store->price_table_id == $table->id ? 'selected' : '' }}>
                                {{ $table->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="can_use_boleto" value="1"
                        {{ $store->can_use_boleto ? 'checked' : '' }}
                        class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                    <span class="text-sm font-medium text-gray-700">Permitir pagamento por boleto</span>
                </label>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Dias para vencimento do boleto</label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="boleto_due_days"
                            value="{{ old('boleto_due_days', $store->boleto_due_days) }}"
                            class="w-24 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        <span class="text-sm text-gray-500">dia(s)</span>
                    </div>
                </div>
            </div>

            {{-- Endereço --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
                <div class="flex items-center gap-2 mb-5">
                    <i class="fas fa-map-location-dot text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Endereço</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">CEP</label>
                        <input type="text" name="address_cep" value="{{ old('address_cep', $store->address_cep) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Logradouro</label>
                        <input type="text" name="address_street" value="{{ old('address_street', $store->address_street) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Número</label>
                        <input type="text" name="address_number" value="{{ old('address_number', $store->address_number) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Complemento</label>
                        <input type="text" name="address_complement" value="{{ old('address_complement', $store->address_complement) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Bairro</label>
                        <input type="text" name="address_district" value="{{ old('address_district', $store->address_district) }}"
                            class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cidade</label>
                            <input type="text" name="address_city" value="{{ old('address_city', $store->address_city) }}"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">UF</label>
                            <input type="text" name="address_state" value="{{ old('address_state', $store->address_state) }}" maxlength="2"
                                class="block w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Horário de Funcionamento --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
                <div class="flex items-center gap-2 mb-5">
                    <i class="fas fa-clock text-green-600 text-sm"></i>
                    <h2 class="text-sm font-semibold text-gray-900">Horário de Funcionamento</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($days as $index => $dayName)
                        @php $hour = $hours->get($index); @endphp
                        <div class="flex items-center gap-3 py-3">
                            <span class="w-8 text-xs font-bold text-gray-500 flex-shrink-0">{{ $dayName }}</span>
                            <input
                                type="checkbox"
                                name="hours[{{ $index }}][is_open]"
                                value="1"
                                {{ ($hour && $hour->is_open) ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500 flex-shrink-0"
                                onchange="toggleHourRow(this, {{ $index }})"
                            >
                            <div class="flex flex-1 items-center gap-2" id="hour-row-{{ $index }}"
                                style="{{ (!$hour || !$hour->is_open) ? 'opacity:0.35;pointer-events:none' : '' }}">
                                <input
                                    type="time"
                                    name="hours[{{ $index }}][open_time]"
                                    value="{{ $hour?->open_time ? \Carbon\Carbon::parse($hour->open_time)->format('H:i') : '' }}"
                                    class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition"
                                >
                                <span class="text-xs text-gray-400 flex-shrink-0">às</span>
                                <input
                                    type="time"
                                    name="hours[{{ $index }}][close_time]"
                                    value="{{ $hour?->close_time ? \Carbon\Carbon::parse($hour->close_time)->format('H:i') : '' }}"
                                    class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition"
                                >
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="flex gap-2 mt-5">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-floppy-disk text-xs"></i> Salvar
            </button>
            <a href="/admin/stores/{{ $store->id }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>

    </form>

</div>

<script>
function toggleHourRow(checkbox, index) {
    const row = document.getElementById('hour-row-' + index);
    row.style.opacity = checkbox.checked ? '1' : '0.35';
    row.style.pointerEvents = checkbox.checked ? 'auto' : 'none';
}
</script>

@endsection
