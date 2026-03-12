@extends('layouts.admin')

@section('title', 'Vincular comércios')

@section('content')
    <h1 class="text-2xl font-bold mb-4">
        Vincular comércio: {{ $client->name }}
    </h1>

    <form action="{{ route('admin.clients.stores.update', $client) }}" method="POST" class="mb-6">
        @csrf
        @method('PUT')

        <!-- pesquisa e filtros -->
        <div class="mb-4 flex flex-col md:flex-row md:items-center md:gap-4">
            <input
                type="text"
                id="storeSearch"
                placeholder="Pesquisar comércios..."
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
            <div class="mt-2 md:mt-0 flex gap-2">
                <button type="button" id="showAll" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Todos
                </button>
                <button type="button" id="showChecked" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Marcados
                </button>
            </div>
        </div>

        <div id="storesList" class="max-h-96 overflow-auto border border-gray-300 rounded-lg p-4">
            @foreach($stores as $store)
                <label class="flex items-center gap-2 mb-2 store-item">
                    <input type="checkbox" name="stores[]" value="{{ $store->id }}"
                        {{ $client->stores->contains($store) ? 'checked' : '' }}
                        class="form-checkbox h-5 w-5 text-indigo-600">
                    <span class="text-sm">{{ $store->name }}</span>
                </label>
            @endforeach
        </div>

        <button type="submit" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            Salvar
        </button>
    </form>

    <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:underline">&larr; Voltar à lista de clientes</a>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('storeSearch');
            const items = document.querySelectorAll('#storesList .store-item');

            search.addEventListener('input', function(e) {
                    const term = e.target.value.toLowerCase();
                    items.forEach(function(label) {
                        const text = label.textContent.toLowerCase();
                        label.style.display = text.includes(term) ? 'flex' : 'none';
                    });
                });

                // filter buttons
                document.getElementById('showAll').addEventListener('click', function() {
                    items.forEach(function(label) { label.style.display = 'flex'; });
                    search.value = '';
                });

                document.getElementById('showChecked').addEventListener('click', function() {
                    items.forEach(function(label) {
                        const checkbox = label.querySelector('input[type=checkbox]');
                        label.style.display = checkbox.checked ? 'flex' : 'none';
                    });
                    search.value = '';
            });
        });
    </script>


@endsection
