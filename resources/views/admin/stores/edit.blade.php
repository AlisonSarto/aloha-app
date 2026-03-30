@extends('layouts.admin')

@section('title', 'Editar Comércio')

@section('content')

@php
    $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
    $hours = $store->storeHours->keyBy('day_of_week');
@endphp

<h1 class="text-3xl font-bold mb-6">
    Editar Comércio
</h1>

{{-- Identificação (somente leitura) --}}
<div class="mb-6 rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
    <h2 class="mb-4 font-semibold text-gray-900">Identificação</h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
            <input
                type="text"
                value="{{ $store->cnpj }}"
                disabled
                class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
            <input
                type="text"
                value="{{ $store->legal_name }}"
                disabled
                class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed"
            >
        </div>
    </div>
</div>

<form method="POST" action="/admin/stores/{{ $store->id }}">
    @csrf
    @method('PUT')

    <div class="space-y-6">

        {{-- Configurações Comerciais --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5 space-y-5">
            <h2 class="font-semibold text-gray-900">Configurações Comerciais</h2>

            <!-- Nome -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Comércio</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $store->name) }}"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
            </div>

            <!-- Frete -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Valor do Frete</label>
                <input
                    type="number"
                    step="0.01"
                    name="shipping_amount"
                    value="{{ old('shipping_amount', $store->shipping_amount) }}"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
            </div>

            <!-- Tabela de preço -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tabela de Preço</label>
                <select
                    name="price_table_id"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
                    @foreach($priceTables as $table)
                        <option value="{{ $table->id }}" {{ $store->price_table_id == $table->id ? 'selected' : '' }}>
                            {{ $table->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Boleto -->
            <div class="flex items-center gap-3">
                <input
                    type="checkbox"
                    name="can_use_boleto"
                    value="1"
                    {{ $store->can_use_boleto ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
                <label class="text-sm font-medium text-gray-700">Permitir pagamento por boleto</label>
            </div>

            <!-- Dias do boleto -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dias para vencimento do boleto</label>
                <input
                    type="number"
                    name="boleto_due_days"
                    value="{{ old('boleto_due_days', $store->boleto_due_days) }}"
                    class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500"
                >
            </div>
        </div>

        {{-- Endereço --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 font-semibold text-gray-900">Endereço</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <input type="text" name="address_cep" value="{{ old('address_cep', $store->address_cep) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" name="address_street" value="{{ old('address_street', $store->address_street) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="address_number" value="{{ old('address_number', $store->address_number) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                    <input type="text" name="address_complement" value="{{ old('address_complement', $store->address_complement) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="address_district" value="{{ old('address_district', $store->address_district) }}"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="address_city" value="{{ old('address_city', $store->address_city) }}"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                        <input type="text" name="address_state" value="{{ old('address_state', $store->address_state) }}" maxlength="2"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Horário de Funcionamento --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 font-semibold text-gray-900">Horário de Funcionamento</h2>
            <div class="divide-y divide-gray-50">
                @foreach ($days as $index => $dayName)
                    @php $hour = $hours->get($index); @endphp
                    <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-gray-50">
                        <span class="w-8 text-sm font-medium text-gray-700">{{ $dayName }}</span>
                        <input
                            type="checkbox"
                            name="hours[{{ $index }}][is_open]"
                            value="1"
                            {{ ($hour && $hour->is_open) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                            onchange="toggleHourRow(this, {{ $index }})"
                        >
                        <div class="flex flex-1 items-center gap-1.5" id="hour-row-{{ $index }}"
                            style="{{ (!$hour || !$hour->is_open) ? 'opacity:0.4;pointer-events:none' : '' }}">
                            <input
                                type="time"
                                name="hours[{{ $index }}][open_time]"
                                value="{{ $hour?->open_time ? \Carbon\Carbon::parse($hour->open_time)->format('H:i') : '' }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-green-500 focus:ring-green-500"
                            >
                            <span class="text-xs text-gray-400">às</span>
                            <input
                                type="time"
                                name="hours[{{ $index }}][close_time]"
                                value="{{ $hour?->close_time ? \Carbon\Carbon::parse($hour->close_time)->format('H:i') : '' }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-green-500 focus:ring-green-500"
                            >
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Botões -->
    <div class="flex gap-2 mt-5">
        <button class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
            <i class="fas fa-floppy-disk text-xs"></i> Salvar
        </button>
        <a href="/admin/stores/{{ $store->id }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
            Cancelar
        </a>
    </div>

</form>

<script>
function toggleHourRow(checkbox, index) {
    const row = document.getElementById('hour-row-' + index);
    row.style.opacity = checkbox.checked ? '1' : '0.4';
    row.style.pointerEvents = checkbox.checked ? 'auto' : 'none';
}
</script>

@endsection
