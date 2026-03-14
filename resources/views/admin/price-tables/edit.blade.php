@extends('layouts.admin')

@section('title', 'Editar Tabela de Preços')

@section('content')
    <h1 class="text-3xl font-bold mb-4">
        Editar Tabela de Preços
    </h1>

    <form action="{{ route('admin.price-tables.update', $priceTable) }}" method="POST" id="priceTableForm">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Nome da Tabela</label>
            <input type="text" name="name" id="name" value="{{ old('name', $priceTable->name) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_default" value="1" {{ old('is_default', $priceTable->is_default) ? 'checked' : '' }} class="mr-2">
                Definir como tabela padrão
            </label>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Faixas de Preço</label>
            <div id="rangesContainer">
                <!-- Ranges will be added here -->
            </div>
            <button type="button" id="addRange" class="mt-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                Adicionar Faixa
            </button>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Atualizar Tabela
            </button>
            <a href="{{ route('admin.price-tables') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Cancelar
            </a>
        </div>
    </form>

    <script>
        let rangeIndex = 0;

        function addRange(min = '', max = '', price = '') {
            const container = document.getElementById('rangesContainer');
            const div = document.createElement('div');
            div.className = 'range-item flex gap-2 mb-2 items-end';
            div.innerHTML = `
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Quantidade Mínima</label>
                    <input type="number" name="ranges[${rangeIndex}][min_quantity]" value="${min}" required min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Quantidade Máxima (opcional)</label>
                    <input type="number" name="ranges[${rangeIndex}][max_quantity]" value="${max}" min="1" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">Preço Unitário</label>
                    <input type="number" name="ranges[${rangeIndex}][unit_price]" value="${price}" required step="0.01" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="button" onclick="removeRange(this)" class="bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-700">
                    <i class="fa-solid fa-trash"></i>
                </button>
            `;
            container.appendChild(div);
            rangeIndex++;
        }

        function removeRange(button) {
            button.parentElement.remove();
        }

        document.getElementById('addRange').addEventListener('click', () => addRange());

        // Add existing ranges
        @foreach($priceTable->ranges as $range)
            addRange('{{ $range->min_quantity }}', '{{ $range->max_quantity }}', '{{ $range->unit_price }}');
        @endforeach

        // If no ranges, add one
        if (rangeIndex === 0) {
            addRange();
        }
    </script>
@endsection
