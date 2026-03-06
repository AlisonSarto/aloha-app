@extends('layouts.admin')

@section('title', 'Editar Comércio')

@section('content')

<h1 class="text-3xl font-bold mb-6">
    Editar Comércio
</h1>

<form method="POST" action="/admin/stores/{{ $store->id }}">
    @csrf
    @method('PUT')

    <div class="bg-white border border-gray-300 rounded-lg p-6 space-y-6">

        <!-- Nome -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Nome do Comércio
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $store->name) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
        </div>


        <!-- Valor de Entrega -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Valor do Frete
            </label>

            <input
                type="number"
                step="0.01"
                name="shipping_amount"
                value="{{ old('shipping_amount', $store->shipping_amount) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
        </div>


        <!-- Tabela de preço -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Tabela de Preço
            </label>

            <select
                name="price_table_id"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >

                @foreach($priceTables as $table)

                    <option
                        value="{{ $table->id }}"
                        {{ $store->price_table_id == $table->id ? 'selected' : '' }}
                    >
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
                class="w-4 h-4"
            >

            <label class="text-sm font-semibold text-gray-700">
                Permitir pagamento por boleto
            </label>

        </div>


        <!-- Dias do boleto -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Dias para vencimento do boleto
            </label>

            <input
                type="number"
                name="boleto_due_days"
                value="{{ old('boleto_due_days', $store->boleto_due_days) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
            >
        </div>


    </div>


    <!-- Botões -->
    <div class="flex gap-2 mt-6">

        <button
            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700"
        >
            Salvar
        </button>

        <a
            href="/admin/stores"
            class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300"
        >
            Cancelar
        </a>

    </div>

</form>

@endsection
