@extends('layouts.admin')
@section('title', 'Criar Cupom')
@section('content')

<h1 class="text-3xl font-bold mb-6">Criar Cupom</h1>

<form method="POST" action="{{ route('admin.coupons.store') }}">
    @csrf

    <div class="bg-white border border-gray-300 rounded-lg p-6 space-y-6">

        {{-- Código --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Código <span class="text-red-500">*</span></label>
            <input type="text" name="code" value="{{ old('code') }}"
                placeholder="Ex: PROMO10"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none uppercase"
                style="text-transform: uppercase;">
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-400 text-xs mt-1">O código será armazenado em letras maiúsculas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tipo de desconto --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de desconto <span class="text-red-500">*</span></label>
                <select name="discount_type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Selecione...</option>
                    <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Percentual (%)</option>
                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixo (R$)</option>
                </select>
                @error('discount_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Valor do desconto --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor do desconto <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" value="{{ old('discount_value') }}"
                    step="0.01" min="0" placeholder="0.00"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('discount_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Pedido mínimo --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor mínimo do pedido (R$)</label>
                <input type="number" name="min_order_value" value="{{ old('min_order_value') }}"
                    step="0.01" min="0" placeholder="Opcional"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('min_order_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Desconto máximo --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Desconto máximo (R$)</label>
                <input type="number" name="max_discount" value="{{ old('max_discount') }}"
                    step="0.01" min="0" placeholder="Opcional"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('max_discount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1">Teto de desconto para cupons percentuais.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Limite total de uso --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Limite total de usos</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                    min="1" placeholder="Sem limite"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('usage_limit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Limite por usuário --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Limite por usuário</label>
                <input type="number" name="usage_per_user" value="{{ old('usage_per_user') }}"
                    min="1" placeholder="Sem limite"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('usage_per_user')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Data de início --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Data de início</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('starts_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data de expiração --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Data de expiração</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('expires_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Ativo --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', '1') ? 'checked' : '' }}
                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-semibold text-gray-700">Cupom ativo</label>
        </div>

    </div>

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Criar Cupom
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
            Cancelar
        </a>
    </div>
</form>

@endsection
