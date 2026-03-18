@extends('layouts.client')

@section('title', 'Minhas Lojas')

@section('content')
    @php $stores = auth()->user()->client->stores; @endphp

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Minhas Lojas</h1>

    <div class="space-y-3">
        @foreach ($stores as $store)
            <div
                class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm ring-1 {{ session('store_id') == $store->id ? 'ring-2 ring-green-500' : 'ring-black/5' }}">
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-gray-900">{{ $store->name }}</p>
                    <p class="truncate text-sm text-gray-500">{{ $store->address_district }}, {{ $store->address_city }}</p>
                </div>

                @if (session('store_id') == $store->id)
                    <span
                        class="ml-3 flex-shrink-0 inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                        <i class="fas fa-check text-xs"></i> Ativa
                    </span>
                @else
                    <form method="POST" action="{{ route('client.set.store') }}" class="ml-3 flex-shrink-0">
                        @csrf
                        <input type="hidden" name="store_id" value="{{ $store->id }}">
                        <button type="submit"
                            class="rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                            Selecionar
                        </button>
                    </form>
                @endif
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

@endsection
