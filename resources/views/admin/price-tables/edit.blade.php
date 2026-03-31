@extends('layouts.admin')

@section('title', 'Editar Tabela de Preços')

@section('content')

    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Tabela de Preços</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $priceTable->name }}</p>
        </div>
        <a href="{{ route('admin.price-tables.show', $priceTable) }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
    </div>

    <form action="{{ route('admin.price-tables.update', $priceTable) }}" method="POST" id="priceTableForm">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 space-y-5 mb-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome da Tabela <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $priceTable->name) }}" required
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="is_default" value="1"
                    {{ old('is_default', $priceTable->is_default) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="text-sm font-medium text-gray-700">Definir como tabela padrão</span>
            </label>

        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 mb-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Faixas de Preço</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Adicione faixas de quantidade e defina o preço unitário de cada.</p>
                </div>
                <button type="button" id="addRange"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700 transition">
                    <i class="fas fa-plus text-xs"></i> Adicionar Faixa
                </button>
            </div>
            <div id="rangesContainer" class="space-y-3"></div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-floppy-disk text-xs"></i> Atualizar Tabela
            </button>
            <a href="{{ route('admin.price-tables.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>

    <script>
        let rangeIndex = 0;
        const inputClass = 'w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition';

        function addRange(min, max, price) {
            min = min || ''; max = max || ''; price = price || '';
            const container = document.getElementById('rangesContainer');
            const div = document.createElement('div');
            div.className = 'range-item grid grid-cols-[1fr_1fr_1fr_auto] gap-3 items-end p-3 rounded-xl bg-gray-50 ring-1 ring-black/5';
            div.innerHTML =
                '<div>' +
                    '<label class="block text-xs font-medium text-gray-600 mb-1">Qtd. Mínima</label>' +
                    '<input type="number" name="ranges[' + rangeIndex + '][min_quantity]" value="' + min + '" required min="1" class="' + inputClass + '">' +
                '</div>' +
                '<div>' +
                    '<label class="block text-xs font-medium text-gray-600 mb-1">Qtd. Máxima <span class="text-gray-400">(opcional)</span></label>' +
                    '<input type="number" name="ranges[' + rangeIndex + '][max_quantity]" value="' + max + '" min="1" class="' + inputClass + '">' +
                '</div>' +
                '<div>' +
                    '<label class="block text-xs font-medium text-gray-600 mb-1">Preço Unitário</label>' +
                    '<input type="number" name="ranges[' + rangeIndex + '][unit_price]" value="' + price + '" required step="0.01" min="0" class="' + inputClass + '">' +
                '</div>' +
                '<button type="button" onclick="this.closest(\'.range-item\').remove()" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 transition flex-shrink-0">' +
                    '<i class="fas fa-trash text-xs"></i>' +
                '</button>';
            container.appendChild(div);
            rangeIndex++;
        }

        document.getElementById('addRange').addEventListener('click', function () { addRange(); });

        @foreach($priceTable->ranges as $range)
            addRange('{{ $range->min_quantity }}', '{{ $range->max_quantity }}', '{{ $range->unit_price }}');
        @endforeach

        if (rangeIndex === 0) { addRange(); }
    </script>

@endsection
