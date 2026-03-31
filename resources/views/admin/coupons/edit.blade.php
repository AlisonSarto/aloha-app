@extends('layouts.admin')
@section('title', 'Editar Cupom')
@section('content')

    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Cupom</h1>
            <p class="text-sm text-gray-500 mt-0.5 font-mono tracking-wider">{{ $coupon->code }}</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            {{-- Código (read-only) --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Identificação</h2>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Código</label>
                    <input type="text" value="{{ $coupon->code }}" disabled
                        class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono text-gray-400 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">O código não pode ser alterado após a criação.</p>
                </div>
            </div>

            {{-- Desconto --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">Desconto</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de desconto <span class="text-red-500">*</span></label>
                        <select name="discount_type" id="discount_type"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition"
                            onchange="toggleDiscountValue(this.value)">
                            <option value="percent"  {{ old('discount_type', $coupon->discount_type) === 'percent'  ? 'selected' : '' }}>Percentual (%)</option>
                            <option value="fixed"    {{ old('discount_type', $coupon->discount_type) === 'fixed'    ? 'selected' : '' }}>Fixo (R$)</option>
                            <option value="shipping" {{ old('discount_type', $coupon->discount_type) === 'shipping' ? 'selected' : '' }}>Frete Grátis</option>
                        </select>
                        @error('discount_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div id="discount_value_wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Valor do desconto <span class="text-red-500">*</span></label>
                        <input type="number" name="discount_value"
                            value="{{ old('discount_value', $coupon->discount_value) }}"
                            step="0.01" min="0"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('discount_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400" id="discount_value_hint"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Valor mínimo do pedido (R$)</label>
                        <input type="number" name="min_order_value"
                            value="{{ old('min_order_value', $coupon->min_order_value) }}"
                            step="0.01" min="0" placeholder="Opcional"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('min_order_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Desconto máximo (R$)</label>
                        <input type="number" name="max_discount"
                            value="{{ old('max_discount', $coupon->max_discount) }}"
                            step="0.01" min="0" placeholder="Opcional"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('max_discount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Limites e Vigência --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">Limites e Vigência</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Limite total de usos</label>
                        <input type="number" name="usage_limit"
                            value="{{ old('usage_limit', $coupon->usage_limit) }}"
                            min="1" placeholder="Sem limite"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('usage_limit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Limite por usuário</label>
                        <input type="number" name="usage_per_user"
                            value="{{ old('usage_per_user', $coupon->usage_per_user) }}"
                            min="1" placeholder="Sem limite"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('usage_per_user') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de início</label>
                        <input type="datetime-local" name="starts_at"
                            value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('starts_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Data de expiração</label>
                        <input type="datetime-local" name="expires_at"
                            value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                        @error('expires_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-x-6 gap-y-3 pt-1">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">Cupom ativo</span>
                    </label>
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_public" id="is_public" value="1"
                            {{ old('is_public', $coupon->is_public) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">Visível publicamente</span>
                        <span class="text-xs text-gray-400">(aparece na lista de cupons do cliente)</span>
                    </label>
                </div>
            </div>

            {{-- Stores --}}
            @php $linkedStores = $coupon->stores->keyBy('id'); @endphp
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Comércios com acesso</h2>
                <p class="text-xs text-gray-500 mb-4">Se nenhum comércio for selecionado, o cupom ficará disponível para todos.</p>
                <div class="space-y-2">
                    @foreach($stores as $store)
                        @php
                            $isLinked = $linkedStores->has($store->id);
                            $oldIds   = old('store_ids', null);
                            $checked  = $oldIds !== null ? in_array($store->id, $oldIds) : $isLinked;
                            $oldLimit = old('store_usage_limits.' . $store->id, $isLinked ? $linkedStores[$store->id]->pivot->usage_limit : '');
                        @endphp
                        <div class="flex items-center gap-4 rounded-xl px-4 py-3 ring-1 ring-black/5 bg-gray-50">
                            <input type="checkbox" name="store_ids[]" id="store_{{ $store->id }}" value="{{ $store->id }}"
                                {{ $checked ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500"
                                onchange="toggleStoreLimit({{ $store->id }}, this.checked)">
                            <label for="store_{{ $store->id }}" class="flex-1 text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $store->name }}
                            </label>
                            <div id="store_limit_{{ $store->id }}" class="{{ $checked ? '' : 'hidden' }} flex items-center gap-2">
                                <label class="text-xs text-gray-500 whitespace-nowrap">Limite p/ comércio:</label>
                                <input type="number" name="store_usage_limits[{{ $store->id }}]"
                                    value="{{ $oldLimit }}"
                                    min="1" placeholder="Sem limite"
                                    class="w-28 rounded-lg border border-gray-200 bg-white px-2 py-1.5 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('store_ids') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-floppy-disk text-xs"></i> Salvar Alterações
            </button>
            <a href="{{ route('admin.coupons.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
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
            document.getElementById('store_limit_' + storeId).classList.toggle('hidden', !checked);
        }
        document.addEventListener('DOMContentLoaded', function () {
            var sel = document.getElementById('discount_type');
            if (sel) toggleDiscountValue(sel.value);
        });
    </script>

@endsection
