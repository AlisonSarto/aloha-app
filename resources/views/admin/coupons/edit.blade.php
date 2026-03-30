@extends('layouts.admin')
@section('title', 'Editar Cupom')
@section('content')

<h1 class="text-3xl font-bold mb-6">Editar Cupom</h1>

<form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
    @csrf
    @method('PUT')

    <div class="bg-white border border-gray-300 rounded-lg p-6 space-y-6">

        {{-- Código (somente leitura) --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Código</label>
            <input type="text" value="{{ $coupon->code }}" disabled
                class="w-full border border-gray-200 bg-gray-50 rounded-lg px-4 py-2 text-gray-500 cursor-not-allowed font-mono">
            <p class="text-gray-400 text-xs mt-1">O código do cupom não pode ser alterado após a criação.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Tipo de desconto --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de desconto <span class="text-red-500">*</span></label>
                <select name="discount_type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="percent" {{ old('discount_type', $coupon->discount_type) === 'percent' ? 'selected' : '' }}>Percentual (%)</option>
                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>Fixo (R$)</option>
                </select>
                @error('discount_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Valor do desconto --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor do desconto <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" value="{{ old('discount_value', $coupon->discount_value) }}"
                    step="0.01" min="0"
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
                <input type="number" name="min_order_value" value="{{ old('min_order_value', $coupon->min_order_value) }}"
                    step="0.01" min="0" placeholder="Opcional"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('min_order_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Desconto máximo --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Desconto máximo (R$)</label>
                <input type="number" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}"
                    step="0.01" min="0" placeholder="Opcional"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('max_discount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Limite total de uso --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Limite total de usos</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}"
                    min="1" placeholder="Sem limite"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('usage_limit')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Limite por usuário --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Limite por usuário</label>
                <input type="number" name="usage_per_user" value="{{ old('usage_per_user', $coupon->usage_per_user) }}"
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
                <input type="datetime-local" name="starts_at"
                    value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('starts_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Data de expiração --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Data de expiração</label>
                <input type="datetime-local" name="expires_at"
                    value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('expires_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Ativo --}}
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
            <label for="is_active" class="text-sm font-semibold text-gray-700">Cupom ativo</label>
        </div>

    </div>

    <div class="flex gap-2 mt-6">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Salvar Alterações
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
            Cancelar
        </a>
    </div>
</form>

@endsection
