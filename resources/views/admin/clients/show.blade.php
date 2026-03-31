@extends('layouts.admin')

@section('title', 'Vincular comércios')

@section('content')

    {{-- Page header --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vincular Comércios</h1>
            <p class="text-sm text-gray-500 mt-0.5">Cliente: <span class="font-medium text-gray-700">{{ $client->name }}</span></p>
        </div>
        <a href="{{ route('admin.clients.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5">

        {{-- Filter controls --}}
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="relative flex-1">
                <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" id="storeSearch"
                    placeholder="Pesquisar comércios..."
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 pl-9 pr-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
            </div>
            <div class="flex gap-2">
                <button type="button" id="showAll"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    <i class="fas fa-list text-xs mr-1"></i> Todos
                </button>
                <button type="button" id="showChecked"
                    class="rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm font-medium text-green-700 hover:bg-green-100 transition">
                    <i class="fas fa-check text-xs mr-1"></i> Marcados
                </button>
            </div>
        </div>

        <form action="{{ route('admin.clients.stores.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Stores list --}}
            <div id="storesList" class="max-h-80 overflow-auto divide-y divide-gray-50 rounded-xl border border-gray-100">
                @foreach($stores as $store)
                    <label class="store-item flex items-center gap-3 px-4 py-3 hover:bg-green-50/50 cursor-pointer transition">
                        <input type="checkbox" name="stores[]" value="{{ $store->id }}"
                            {{ $client->stores->contains($store) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm text-gray-800">{{ $store->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-5 flex items-center gap-3">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                    <i class="fas fa-floppy-disk text-xs"></i> Salvar
                </button>
                <a href="{{ route('admin.clients.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('storeSearch');
            const items  = document.querySelectorAll('#storesList .store-item');

            search.addEventListener('input', function (e) {
                const term = e.target.value.toLowerCase();
                items.forEach(function (label) {
                    label.style.display = label.textContent.toLowerCase().includes(term) ? 'flex' : 'none';
                });
            });

            document.getElementById('showAll').addEventListener('click', function () {
                items.forEach(function (label) { label.style.display = 'flex'; });
                search.value = '';
            });

            document.getElementById('showChecked').addEventListener('click', function () {
                items.forEach(function (label) {
                    label.style.display = label.querySelector('input[type=checkbox]').checked ? 'flex' : 'none';
                });
                search.value = '';
            });
        });
    </script>

@endsection
