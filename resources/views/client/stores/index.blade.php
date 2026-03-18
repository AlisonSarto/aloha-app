@extends('layouts.client')

@section('title', 'Minhas Lojas')

@section('content')
    @php $stores = auth()->user()->client->stores; @endphp

    <h1 class="mb-4 text-2xl font-bold text-gray-900">Minhas Lojas</h1>

    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-3">
        @foreach ($stores as $store)
            <div
                class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 {{ session('store_id') == $store->id ? 'ring-2 ring-green-500' : 'ring-black/5' }}">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900">{{ $store->name }}</p>
                    <p class="truncate text-sm text-gray-500">{{ $store->address_district }}, {{ $store->address_city }}</p>
                </div>

                <div class="ml-3 flex flex-shrink-0 items-center gap-2">
                    <a href="{{ route('client.stores.edit', $store) }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-gray-500 shadow-sm transition-colors hover:border-green-500 hover:text-green-600">
                        <i class="fas fa-pen text-xs"></i>
                    </a>

                    <button type="button"
                        onclick="openUnlinkModal({{ $store->id }}, '{{ addslashes($store->name) }}')"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-1.5 text-gray-400 shadow-sm transition-colors hover:border-red-400 hover:text-red-500">
                        <i class="fas fa-link-slash text-xs"></i>
                    </button>

                    @if (session('store_id') == $store->id)
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                            <i class="fas fa-check text-xs"></i> Ativa
                        </span>
                    @else
                        <form method="POST" action="{{ route('client.set.store') }}">
                            @csrf
                            <input type="hidden" name="store_id" value="{{ $store->id }}">
                            <button type="submit"
                                class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                                Selecionar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a href="{{ route('client.stores.register') }}"
            class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 py-4 text-sm font-medium text-gray-500 transition-colors hover:border-green-500 hover:text-green-600">
            <i class="fas fa-plus"></i>
            Vincular nova loja
        </a>
    </div>

    {{-- Modal de confirmação de desvincular --}}
    <div id="unlink-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-triangle-exclamation text-red-500"></i>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Desvincular loja</h2>
            </div>

            <p class="mb-1 text-sm text-gray-700">
                Deseja desvincular <span id="unlink-store-name" class="font-semibold"></span> da sua conta?
            </p>

            <div class="my-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700 ring-1 ring-amber-200">
                <p class="flex items-start gap-2">
                    <i class="fas fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>O histórico da loja <strong>não será apagado</strong>. Você pode vincular esta loja novamente a qualquer momento usando o CNPJ.</span>
                </p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeUnlinkModal()"
                    class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    Cancelar
                </button>
                <form id="unlink-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="flex-1 rounded-xl bg-red-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-600">
                        Desvincular
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUnlinkModal(storeId, storeName) {
            document.getElementById('unlink-store-name').textContent = storeName;
            document.getElementById('unlink-form').action = '/client/stores/' + storeId + '/unlink';
            const modal = document.getElementById('unlink-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeUnlinkModal() {
            const modal = document.getElementById('unlink-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.getElementById('unlink-modal').addEventListener('click', function(e) {
            if (e.target === this) closeUnlinkModal();
        });
    </script>

@endsection
