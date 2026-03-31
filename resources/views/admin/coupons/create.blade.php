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
                <select name="discount_type" id="discount_type"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                    onchange="toggleDiscountValue(this.value)">
                    <option value="">Selecione...</option>
                    <option value="percent"  {{ old('discount_type') === 'percent'  ? 'selected' : '' }}>Percentual (%)</option>
                    <option value="fixed"    {{ old('discount_type') === 'fixed'    ? 'selected' : '' }}>Fixo (R$)</option>
                    <option value="shipping" {{ old('discount_type') === 'shipping' ? 'selected' : '' }}>Frete Grátis</option>
                </select>
                @error('discount_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Valor do desconto --}}
            <div id="discount_value_wrap">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Valor do desconto <span class="text-red-500">*</span></label>
                <input type="number" name="discount_value" value="{{ old('discount_value', 0) }}"
                    step="0.01" min="0" placeholder="0.00"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                @error('discount_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-1" id="discount_value_hint"></p>
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

        {{-- Flags --}}
        <div class="flex flex-wrap items-center gap-6">
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', '1') ? 'checked' : '' }}
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-semibold text-gray-700">Cupom ativo</label>
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_public" id="is_public" value="1"
                    {{ old('is_public') ? 'checked' : '' }}
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_public" class="text-sm font-semibold text-gray-700">Visível publicamente</label>
                <span class="text-xs text-gray-400">(aparece na lista de cupons do cliente)</span>
            </div>
        </div>

        {{-- Stores liberadas --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Stores com acesso</label>
            <p class="text-xs text-gray-400 mb-3">Se nenhuma store for selecionada, o cupom ficará disponível para todas as stores.</p>
            <div class="space-y-3">
                @foreach($stores as $store)
                    @php $checked = in_array($store->id, old('store_ids', [])); @endphp
                    <div class="flex items-center gap-4 p-3 border border-gray-200 rounded-lg">
                        <input type="checkbox" name="store_ids[]" id="store_{{ $store->id }}" value="{{ $store->id }}"
                            {{ $checked ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 store-checkbox"
                            onchange="toggleStoreLimit({{ $store->id }}, this.checked)">
                        <label for="store_{{ $store->id }}" class="flex-1 text-sm font-medium text-gray-700">
                            {{ $store->name }}
                        </label>
                        <div id="store_limit_{{ $store->id }}" class="{{ $checked ? '' : 'hidden' }} flex items-center gap-2">
                            <label class="text-xs text-gray-500">Limite por store:</label>
                            <input type="number" name="store_usage_limits[{{ $store->id }}]"
                                value="{{ old('store_usage_limits.' . $store->id) }}"
                                min="1" placeholder="Sem limite"
                                class="w-28 border border-gray-300 rounded px-2 py-1 text-sm focus:ring-1 focus:ring-indigo-500 focus:outline-none">
                        </div>
                    </div>
                @endforeach
            </div>
            @error('store_ids')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
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

<script>
function toggleDiscountValue(type) {
    const wrap = document.getElementById('discount_value_wrap');
    const hint = document.getElementById('discount_value_hint');
    if (type === 'shipping') {
        wrap.classList.add('opacity-40', 'pointer-events-none');
        hint.textContent = 'Para cupom de frete grátis, o valor é ignorado.';
    } else {
        wrap.classList.remove('opacity-40', 'pointer-events-none');
        hint.textContent = '';
    }
}

function toggleStoreLimit(storeId, checked) {
    const el = document.getElementById('store_limit_' + storeId);
    el.classList.toggle('hidden', !checked);
}

// Run on load for old() values
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('discount_type');
    if (sel) toggleDiscountValue(sel.value);
});
</script>

@endsection
