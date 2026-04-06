@extends('layouts.seller')

@section('title', 'Minhas Lojas')

@section('content')

    <div class="flex items-center justify-between mb-6 mt-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Minhas Lojas</h1>
            <p class="text-sm text-gray-500 mt-0.5">Lojas que você cadastrou</p>
        </div>
        <a href="{{ route('seller.stores.register') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition shadow-sm">
            <i class="fas fa-plus text-xs"></i> Nova Loja
        </a>
    </div>

    @if($stores->isEmpty())
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-12 text-center">
            <i class="fas fa-store text-gray-300 text-4xl mb-4"></i>
            <p class="text-gray-500 font-medium">Você ainda não cadastrou nenhuma loja.</p>
            <a href="{{ route('seller.stores.register') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition">
                <i class="fas fa-plus text-xs"></i> Cadastrar primeira loja
            </a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($stores as $store)
                <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-4 flex items-center justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $store->name }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $store->seller_assignment_status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($store->seller_assignment_status === 'pending'  ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ match($store->seller_assignment_status) {
                                    'approved' => 'Aprovada',
                                    'pending'  => 'Aguardando aprovação',
                                    'rejected' => 'Rejeitada',
                                    default    => '—'
                                } }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $store->address_city }}/{{ $store->address_state }} · CNPJ: {{ substr($store->cnpj, 0, 2) }}.{{ substr($store->cnpj, 2, 3) }}.{{ substr($store->cnpj, 5, 3) }}/{{ substr($store->cnpj, 8, 4) }}-{{ substr($store->cnpj, 12) }}</p>
                        @if($store->seller_assignment_status === 'rejected' && $store->seller_assignment_reason)
                            <p class="text-xs text-red-600 mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $store->seller_assignment_reason }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(!$store->hasLinkedClients())
                            <a href="{{ route('seller.stores.edit', $store) }}"
                               class="text-xs rounded-lg bg-gray-100 px-3 py-2 text-gray-700 hover:bg-gray-200 transition font-medium">
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
