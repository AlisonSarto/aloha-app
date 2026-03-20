@extends('layouts.client')

@section('title', 'Fazer Pedido')

@section('content')

    <div class="select-none">

        {{-- ── PROGRESS HEADER ──────────────────────────────────────────────── --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 id="step-title" class="text-2xl font-bold text-gray-900">Escolha os sabores</h1>
                    <p id="step-subtitle" class="text-sm text-gray-500 mt-0.5">Passo 1 de 3</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <span id="step-icon" class="text-green-700 font-bold text-lg">1</span>
                </div>
            </div>

            <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div id="progress-bar" class="h-full bg-green-600 rounded-full transition-all duration-500 ease-out" style="width: 33.33%"></div>
            </div>

            <div class="flex justify-between mt-1.5">
                <span id="lbl-1" class="text-xs font-semibold text-green-600">Sabores</span>
                <span id="lbl-2" class="text-xs text-gray-400">Entrega</span>
                <span id="lbl-3" class="text-xs text-gray-400">Confirmação</span>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 1: SABORES                                                     --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="step-1" class="pb-64">

            {{-- Incentive message --}}
            <div id="incentive-msg"
                class="hidden mb-4 rounded-2xl bg-gradient-to-r from-amber-50 via-orange-50 to-amber-100
                    border border-amber-300 px-4 py-3 shadow-sm">

                <div class="flex items-start gap-3">

                    <!-- Icon -->
                    <div class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center shadow">
                        <i class="fas fa-fire text-white text-sm"></i>
                    </div>

                    <!-- Text -->
                    <div>
                        <p class="text-sm font-semibold text-amber-900">
                            Oferta especial
                        </p>
                        <p id="incentive-text" class="text-sm text-amber-800 leading-snug mt-0.5"></p>
                    </div>

                </div>
            </div>

            <div class="space-y-2.5">
                @foreach ($flavors as $flavor)
                    <div id="card-{{ $flavor['id'] }}"
                         class="flex items-center justify-between rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-black/5 transition-all duration-150">

                        {{-- Icon + name --}}
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                                 style="background-color: {{ $flavor['color'] }}22;">
                                {{ $flavor['emoji'] }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 text-sm">{{ $flavor['name'] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate" id="phint-{{ $flavor['id'] }}">—</p>
                            </div>
                        </div>

                        {{-- Qty controls --}}
                        <div class="flex items-center gap-1.5 flex-shrink-0 ml-3">
                            <button type="button"
                                    onclick="changeQty('{{ $flavor['id'] }}', -1)"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-600 transition-all active:scale-90">
                                <i class="fas fa-minus text-xs"></i>
                            </button>

                            <input type="number"
                                   id="qty-{{ $flavor['id'] }}"
                                   value="0"
                                   min="0"
                                   inputmode="numeric"
                                   onchange="setQty('{{ $flavor['id'] }}', this.value)"
                                   oninput="setQty('{{ $flavor['id'] }}', this.value)"
                                   class="w-14 text-center font-bold text-gray-900 text-sm border border-gray-200 rounded-lg py-1 focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-colors">

                            <button type="button"
                                    onclick="changeQty('{{ $flavor['id'] }}', 1)"
                                    class="w-8 h-8 rounded-lg bg-green-600 hover:bg-green-700 flex items-center justify-center text-white transition-all active:scale-90">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Fixed summary panel (sits above bottom navbar) --}}
            <div id="summary-panel" class="fixed bottom-24 inset-x-0 mx-auto max-w-lg px-4 z-40">
                <div class="bg-white rounded-2xl shadow-2xl ring-1 ring-black/10 p-4">

                    {{-- Totals row --}}
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Total</p>
                            <p class="font-bold text-gray-900 text-sm">
                                <span id="total-qty-display">0</span> unidades
                            </p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Preço/un</p>
                            <p id="unit-price-display" class="font-bold text-green-600 text-sm">—</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Subtotal</p>
                            <p id="subtotal-display" class="font-bold text-gray-900 text-lg">R$&nbsp;0,00</p>
                        </div>
                    </div>

                    {{-- Continue button --}}
                    <button id="btn-step1"
                            onclick="goStep(2)"
                            disabled
                            class="w-full rounded-xl bg-green-600 text-white py-3 font-semibold text-sm hover:bg-green-700 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">
                        Continuar <i class="fas fa-arrow-right ml-1.5"></i>
                    </button>
                </div>
            </div>

        </div>{{-- /step-1 --}}

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 2: ENTREGA + PAGAMENTO                                         --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="step-2" class="hidden space-y-4 pb-6">

            {{-- Tipo de entrega --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-truck text-green-600 mr-1.5"></i> Tipo de entrega
                </h3>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="btn-delivery"
                            onclick="setDeliveryType('delivery')"
                            class="flex flex-col items-center gap-2 rounded-xl border-2 border-green-500 bg-green-50 p-4 transition-all">
                        <i class="fas fa-truck text-2xl text-green-600"></i>
                        <span class="text-sm font-semibold text-green-700">Entrega</span>
                    </button>

                    <button type="button" id="btn-pickup"
                            onclick="setDeliveryType('pickup')"
                            class="flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 bg-white p-4 transition-all hover:border-gray-300">
                        <i class="fas fa-store text-2xl text-gray-400"></i>
                        <span class="text-sm font-semibold text-gray-500">Retirada</span>
                    </button>
                </div>

                <div id="shipping-row" class="mt-3 flex items-center justify-between rounded-lg bg-green-50 px-3 py-2 ring-1 ring-green-200">
                    <span class="text-sm text-green-700">
                        <i class="fas fa-route mr-1.5"></i> Frete
                    </span>
                    <span class="text-sm font-bold text-green-700">
                        @if(($store->shipping_amount ?? 0) == 0)
                            Frete grátis
                        @else
                            R$ {{ number_format((float) $store->shipping_amount, 2, ',', '.') }}
                        @endif
                    </span>
                </div>
            </div>

            {{-- Data --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-calendar-day text-green-600 mr-1.5"></i>
                    <span id="date-label">Data de entrega</span>
                </h3>

                <input type="date"
                       id="delivery-date"
                       class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 transition-colors">

                <p id="date-hint" class="text-xs text-gray-400 mt-1.5"></p>
            </div>

            {{-- Pagamento --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-credit-card text-green-600 mr-1.5"></i> Forma de pagamento
                </h3>

                <div class="space-y-2.5">

                    {{-- Pix --}}
                    <button type="button" id="pay-pix" onclick="setPayment('pix')"
                            class="pay-btn w-full flex items-center gap-3 rounded-xl border-2 border-gray-200 p-3.5 text-left transition-all hover:border-green-300">
                        <span class="text-2xl leading-none">⚡</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">Pix</p>
                            <p class="text-xs text-gray-400">Pagamento instantâneo</p>
                        </div>
                        <i id="check-pix" class="fas fa-circle-check text-green-600 text-lg hidden"></i>
                    </button>

                    {{-- Boleto --}}
                    @if ($store->can_use_boleto)
                        <button type="button" id="pay-boleto" onclick="setPayment('boleto')"
                                class="pay-btn w-full flex items-center gap-3 rounded-xl border-2 border-gray-200 p-3.5 text-left transition-all hover:border-green-300">
                            <span class="text-2xl leading-none">📄</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">Boleto</p>
                                <p class="text-xs text-gray-400">Vence em {{ $store->boleto_due_days ?? 3 }} dia(s) úteis após a entrega</p>
                            </div>
                            <i id="check-boleto" class="fas fa-circle-check text-green-600 text-lg hidden"></i>
                        </button>
                    @else
                        <div class="w-full flex items-center gap-3 rounded-xl border-2 border-dashed border-gray-200 p-3.5 opacity-50 select-none">
                            <span class="text-2xl leading-none grayscale">📄</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-500">
                                    Boleto
                                    <span class="ml-1.5 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-500">indisponível</span>
                                </p>
                                <p class="text-xs text-gray-400">Não habilitado para este estabelecimento</p>
                            </div>
                        </div>
                    @endif

                    {{-- Dinheiro --}}
                    <button type="button" id="pay-cash" onclick="setPayment('cash')"
                            class="pay-btn w-full flex items-center gap-3 rounded-xl border-2 border-gray-200 p-3.5 text-left transition-all hover:border-green-300">
                        <span class="text-2xl leading-none">💵</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">Dinheiro</p>
                            <p class="text-xs text-gray-400">Pago na entrega ou retirada</p>
                        </div>
                        <i id="check-cash" class="fas fa-circle-check text-green-600 text-lg hidden"></i>
                    </button>

                    {{-- Dinheiro --}}
                    <button type="button" id="pay-card" onclick="setPayment('card')"
                            class="pay-btn w-full flex items-center gap-3 rounded-xl border-2 border-gray-200 p-3.5 text-left transition-all hover:border-green-300">
                        <span class="text-2xl leading-none">💳</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">Cartão</p>
                            <p class="text-xs text-gray-400">Pago na entrega ou retirada no débito ou crédito</p>
                        </div>
                        <i id="check-card" class="fas fa-circle-check text-green-600 text-lg hidden"></i>
                    </button>

                </div>
            </div>

            {{-- Observações --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">
                    <i class="fas fa-note-sticky text-green-600 mr-1.5"></i> Observações
                    <span class="ml-1.5 text-xs font-normal text-gray-400">(opcional)</span>
                </h3>
                <p class="text-xs text-gray-400 mb-3">Preferências de entrega, combinados especiais… Vamos ler tudo antes de ser entrege!</p>

                <textarea id="order-notes"
                          rows="3"
                          placeholder=""
                          class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 placeholder:text-gray-300 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 resize-none transition-colors"></textarea>
            </div>

            {{-- Nav buttons --}}
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="goStep(1)"
                        class="flex-1 rounded-xl border-2 border-gray-200 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-arrow-left mr-1.5"></i> Voltar
                </button>
                <button type="button" id="btn-step2" onclick="goStep(3)" disabled
                        class="flex-[2] rounded-xl bg-green-600 text-white py-3 text-sm font-semibold hover:bg-green-700 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">
                    Continuar <i class="fas fa-arrow-right ml-1.5"></i>
                </button>
            </div>

        </div>{{-- /step-2 --}}

        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        {{-- STEP 3: CONFIRMAÇÃO                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════════════ --}}
        <div id="step-3" class="hidden space-y-4 pb-6">

            {{-- Sabores selecionados --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-snowflake text-green-600 mr-1.5"></i> Sabores selecionados
                </h3>
                <div id="summary-flavors" class="space-y-2 text-sm"></div>
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between">
                    <span class="text-sm text-gray-500">Subtotal</span>
                    <span class="font-bold text-gray-900" id="sum-subtotal">—</span>
                </div>
            </div>

            {{-- Entrega --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-truck text-green-600 mr-1.5"></i> Entrega
                </h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Tipo</span>
                        <span id="sum-delivery-type" class="font-medium text-gray-900">—</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Data</span>
                        <span id="sum-date" class="font-medium text-gray-900">—</span>
                    </div>
                    <div id="sum-shipping-row" class="flex justify-between">
                        <span class="text-gray-500">Frete</span>
                        <span id="sum-shipping" class="font-medium text-gray-900">—</span>
                    </div>
                </div>
            </div>

            {{-- Pagamento --}}
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-credit-card text-green-600 mr-1.5"></i> Pagamento
                </h3>
                <p id="sum-payment" class="text-sm font-medium text-gray-900">—</p>
            </div>

            {{-- Observação --}}
            <div id="sum-notes-box" class="hidden rounded-xl bg-amber-50 ring-1 ring-amber-200 p-4">
                <h3 class="text-xs font-semibold text-amber-700 mb-1 uppercase tracking-wide">
                    <i class="fas fa-note-sticky mr-1"></i> Observação
                </h3>
                <p id="sum-notes" class="text-sm text-amber-900 leading-snug"></p>
            </div>

            {{-- Total geral --}}
            <div class="rounded-2xl bg-gradient-to-r from-green-600 to-green-700 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-xs font-medium uppercase tracking-wide mb-0.5">Total do pedido</p>
                        <p class="text-white text-2xl font-extrabold" id="sum-total">R$ 0,00</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-receipt text-white text-xl"></i>
                    </div>
                </div>
            </div>

            {{-- Nav buttons --}}
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="goStep(2)"
                        class="flex-1 rounded-xl border-2 border-gray-200 py-3 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-all active:scale-95">
                    <i class="fas fa-arrow-left mr-1.5"></i> Voltar
                </button>
                <button type="button" id="btn-confirm" onclick="confirmOrder()"
                        class="flex-[2] rounded-xl bg-green-600 text-white py-3 text-sm font-semibold hover:bg-green-700 transition-all active:scale-95">
                    <i class="fas fa-check mr-1.5"></i> Confirmar pedido!
                </button>
            </div>

            {{-- Loading / success state --}}
            <div id="confirm-feedback" class="hidden"></div>

        </div>{{-- /step-3 --}}

    </div>{{-- /select-none --}}

    <script>
        // ── DATA FROM PHP ─────────────────────────────────────────────────────
        const FLAVORS = @json($flavors);

        const PRICE_RANGES = @json($priceRangesData);

        console.log(PRICE_RANGES);

        const DELIVERY_CFG = {
            delivery_days:  @json($deliveryConfig->delivery_days),
            lead_days:      {{ (int) $deliveryConfig->lead_days }},
            late_direction: '{{ $deliveryConfig->late_direction }}',
        };

        const STORE = {
            shipping_amount: {{ (float) ($store->shipping_amount ?? 0) }},
        };

        const PAYMENT_LABELS = { pix: 'Pix ⚡', boleto: 'Boleto 📄', cash: 'Dinheiro 💵', card: 'Cartão 💳' };

        // ── STATE ─────────────────────────────────────────────────────────────
        const state = {
            step:         1,
            quantities:   {},
            deliveryType: 'delivery',
            deliveryDate: '',
            payment:      '',
            notes:        '',
        };

        FLAVORS.forEach(f => { state.quantities[f.id] = 0; });

        // ── PRICE HELPERS ─────────────────────────────────────────────────────
        function getRange(qty) {
            if (!PRICE_RANGES.length || qty === 0) return null;
            return PRICE_RANGES.find(r =>
                qty >= r.min_quantity && (r.max_quantity === null || qty <= r.max_quantity)
            ) ?? null;
        }

        function getNextRange(qty) {
            if (!PRICE_RANGES.length) return null;
            return PRICE_RANGES.find(r => r.min_quantity > qty) ?? null;
        }

        function fmt(val) {
            return 'R$\u00a0' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function totalQty() {
            return Object.values(state.quantities).reduce((a, b) => a + b, 0);
        }

        // ── QUANTITY CONTROLS ─────────────────────────────────────────────────
        function changeQty(id, delta) {
            applyQty(id, Math.max(0, (state.quantities[id] || 0) + delta));
        }

        function setQty(id, raw) {
            const v = parseInt(raw, 10);
            applyQty(id, isNaN(v) || v < 0 ? 0 : v);
        }

        function applyQty(id, qty) {
            state.quantities[id] = qty;

            const input = document.getElementById('qty-' + id);
            if (input && +input.value !== qty) input.value = qty;

            const card = document.getElementById('card-' + id);
            if (card) {
                card.classList.toggle('ring-2',       qty > 0);
                card.classList.toggle('ring-green-400', qty > 0);
                card.classList.toggle('ring-black/5',  qty === 0);
            }

            recalculate();
        }

        // ── RECALCULATE ───────────────────────────────────────────────────────
        function recalculate() {
            const qty       = totalQty();
            const range     = getRange(qty);
            const unitPrice = range ? range.unit_price : null;
            const subtotal  = unitPrice ? qty * unitPrice : 0;

            // Per-flavor price hints
            FLAVORS.forEach(f => {
                const hint = document.getElementById('phint-' + f.id);
                if (!hint) return;
                const q = state.quantities[f.id] || 0;
                if (q > 0 && unitPrice) {
                    hint.textContent = q + ' × ' + fmt(unitPrice) + ' = ' + fmt(q * unitPrice);
                    hint.className = 'text-xs text-green-600 mt-0.5 truncate font-medium';
                } else {
                    hint.textContent = q > 0 ? '…' : '—';
                    hint.className = 'text-xs text-gray-400 mt-0.5 truncate';
                }
            });

            // Summary panel numbers
            document.getElementById('total-qty-display').textContent  = qty;
            document.getElementById('unit-price-display').textContent = unitPrice ? fmt(unitPrice) + '/un' : '—';
            document.getElementById('subtotal-display').textContent   = fmt(subtotal);

            // Incentive message
            const next       = getNextRange(qty);
            const incentiveEl = document.getElementById('incentive-msg');
            const incentiveTx = document.getElementById('incentive-text');

            if (next && unitPrice && next.unit_price < unitPrice) {
                const needed  = next.min_quantity - qty;
                incentiveTx.textContent =
                    '🔥 Adicione mais ' + needed + ' pct. e pague ' + fmt(next.unit_price) +
                    '/pct — economize ' + fmt(unitPrice - next.unit_price) + ' por pacote!';
                incentiveEl.classList.remove('hidden');
            } else if (next && !unitPrice && qty > 0) {
                incentiveTx.textContent =
                    'Mais ' + (next.min_quantity - qty) + ' pacotes para entrar na faixa de preço!';
                incentiveEl.classList.remove('hidden');
            } else if (!unitPrice && qty === 0) {
                incentiveTx.textContent = 'Adicione produtos para ver os preços por faixa.';
                incentiveEl.classList.add('hidden');
            } else {
            }

            // Enable step-1 continue button
            document.getElementById('btn-step1').disabled = qty === 0;
        }

        // ── NAVIGATION ────────────────────────────────────────────────────────
        function goStep(n) {
            if (n === 2 && totalQty() === 0) return;
            if (n === 3 && !step2Valid())    return;
            if (n === 3) renderSummary();

            [1, 2, 3].forEach(i => {
                document.getElementById('step-' + i).classList.toggle('hidden', i !== n);
            });

            // Summary panel only on step 1
            document.getElementById('summary-panel').style.display = n === 1 ? '' : 'none';

            state.step = n;
            updateProgress(n);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ── PROGRESS ──────────────────────────────────────────────────────────
        function updateProgress(n) {
            document.getElementById('progress-bar').style.width = (n / 3 * 100) + '%';

            const titles = {
                1: ['Escolha os sabores',  'Passo 1 de 3'],
                2: ['Entrega e pagamento', 'Passo 2 de 3'],
                3: ['Confirme seu pedido', 'Passo 3 de 3'],
            };
            document.getElementById('step-title').textContent    = titles[n][0];
            document.getElementById('step-subtitle').textContent = titles[n][1];
            document.getElementById('step-icon').textContent     = n;

            [1, 2, 3].forEach(i => {
                const el = document.getElementById('lbl-' + i);
                el.className = i <= n
                    ? 'text-xs font-semibold text-green-600'
                    : 'text-xs text-gray-400';
            });
        }

        // ── DELIVERY TYPE ─────────────────────────────────────────────────────
        function setDeliveryType(type) {
            state.deliveryType = type;
            const isDelivery = type === 'delivery';

            const btnD = document.getElementById('btn-delivery');
            const btnP = document.getElementById('btn-pickup');

            btnD.className = 'flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all ' +
                (isDelivery ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300');
            btnD.querySelector('i').className  = 'fas fa-truck text-2xl ' + (isDelivery ? 'text-green-600' : 'text-gray-400');
            btnD.querySelector('span').className = 'text-sm font-semibold ' + (isDelivery ? 'text-green-700' : 'text-gray-500');

            btnP.className = 'flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all ' +
                (!isDelivery ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300');
            btnP.querySelector('i').className  = 'fas fa-store text-2xl ' + (!isDelivery ? 'text-green-600' : 'text-gray-400');
            btnP.querySelector('span').className = 'text-sm font-semibold ' + (!isDelivery ? 'text-green-700' : 'text-gray-500');

            document.getElementById('shipping-row').classList.toggle('hidden', !isDelivery);
            document.getElementById('date-label').textContent = isDelivery ? 'Data de entrega' : 'Data da retirada';

            // Reset date and recompute min
            state.deliveryDate = '';
            document.getElementById('delivery-date').value = '';
            updateDateConstraints();
            validateStep2UI();
        }

        // ── DATE LOGIC ────────────────────────────────────────────────────────
        // App day: 0=Mon … 6=Sun | JS Date.getDay(): 0=Sun … 6=Sat
        function jsToAppDay(d) { return d === 0 ? 6 : d - 1; }

        function toISO(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }

        function formatPT(iso) {
            if (!iso) return '—';
            const [y, m, d] = iso.split('-').map(Number);
            const dt   = new Date(y, m - 1, d);
            const days = ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'];
            return days[dt.getDay()] + ', ' + String(d).padStart(2,'0') + '/' +
                   String(m).padStart(2,'0') + '/' + y;
        }

        function calcMinDate() {
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (state.deliveryType === 'pickup') return today;

            const d = new Date(today);
            d.setDate(d.getDate() + DELIVERY_CFG.lead_days);

            let tries = 0;
            while (!DELIVERY_CFG.delivery_days.includes(jsToAppDay(d.getDay()))) {
                d.setDate(d.getDate() + (DELIVERY_CFG.late_direction === 'after' ? 1 : -1));
                if (++tries > 14) break;
            }

            // Never go before today
            if (d < today) d.setTime(today.getTime());
            return d;
        }

        function updateDateConstraints() {
            const min   = calcMinDate();
            const input = document.getElementById('delivery-date');
            input.min   = toISO(min);

            document.getElementById('date-hint').textContent =
                state.deliveryType === 'delivery'
                    ? 'Data mínima de entrega: ' + formatPT(toISO(min))
                    : 'Pode retirar a partir de hoje.';
        }

        // ── PAYMENT ───────────────────────────────────────────────────────────
        const PAY_IDS = ['pix', 'boleto', 'cash', 'card'];

        function setPayment(method) {
            state.payment = method;

            PAY_IDS.forEach(id => {
                const btn   = document.getElementById('pay-' + id);
                const check = document.getElementById('check-' + id);
                if (!btn) return;

                const active = id === method;
                btn.classList.toggle('border-green-500', active);
                btn.classList.toggle('bg-green-50',      active);
                btn.classList.toggle('border-gray-200',  !active);
                if (check) check.classList.toggle('hidden', !active);
            });

            validateStep2UI();
        }

        // ── STEP 2 VALIDATION ─────────────────────────────────────────────────
        function step2Valid() {
            return state.deliveryDate !== '' && state.payment !== '';
        }

        function validateStep2UI() {
            document.getElementById('btn-step2').disabled = !step2Valid();
        }

        // ── SUMMARY RENDER ────────────────────────────────────────────────────
        function renderSummary() {
            const qty      = totalQty();
            const range    = getRange(qty);
            const uPrice   = range ? range.unit_price : 0;
            const subtotal = uPrice * qty;
            const shipping = state.deliveryType === 'delivery' ? STORE.shipping_amount : 0;
            const total    = subtotal + shipping;

            // Flavor rows
            const container = document.getElementById('summary-flavors');
            container.innerHTML = '';
            FLAVORS.forEach(f => {
                const q = state.quantities[f.id] || 0;
                if (!q) return;
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between py-1';
                row.innerHTML =
                    '<div class="flex items-center gap-2 min-w-0">' +
                        '<span class="text-base leading-none">' + f.emoji + '</span>' +
                        '<span class="text-sm text-gray-700 truncate">' + f.name + '</span>' +
                        '<span class="text-xs text-gray-400 flex-shrink-0">× ' + q + '</span>' +
                    '</div>' +
                    '<span class="text-sm font-semibold text-gray-900 ml-3 flex-shrink-0">' +
                        fmt(q * uPrice) + '</span>';
                container.appendChild(row);
            });

            document.getElementById('sum-subtotal').textContent      = fmt(subtotal);
            document.getElementById('sum-delivery-type').textContent =
                state.deliveryType === 'delivery' ? '🚚 Entrega' : '🏪 Retirada';
            document.getElementById('sum-date').textContent          = formatPT(state.deliveryDate);
            document.getElementById('sum-payment').textContent       = PAYMENT_LABELS[state.payment] || '—';

            const shRow = document.getElementById('sum-shipping-row');
            if (state.deliveryType === 'delivery') {
                shRow.classList.remove('hidden');
                document.getElementById('sum-shipping').textContent = fmt(shipping);
            } else {
                shRow.classList.add('hidden');
            }

            document.getElementById('sum-total').textContent = fmt(total);

            const notesBox = document.getElementById('sum-notes-box');
            const notesEl  = document.getElementById('sum-notes');
            const n        = (document.getElementById('order-notes').value || '').trim();
            state.notes    = n;
            notesEl.textContent = n;
            notesBox.classList.toggle('hidden', !n);
        }

        // ── CONFIRM ORDER ─────────────────────────────────────────────────────
        function confirmOrder() {
            const btn = document.getElementById('btn-confirm');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1.5"></i> Enviando…';

            const flavors = Object.entries(state.quantities)
                .filter(([id, qty]) => qty > 0)
                .map(([id, qty]) => ({ id, qty: Number(qty) }));

            fetch('{{ route("client.orders.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    flavors,
                    delivery_type: state.deliveryType,
                    delivery_date: state.deliveryDate,
                    payment:       state.payment,
                    notes:         state.notes,
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) throw new Error('Falha ao criar pedido');

                console.log(data);

                document.getElementById('step-3').innerHTML =
                    '<div class="flex flex-col items-center gap-5 py-10">' +
                        '<div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center shadow-inner">' +
                            '<i class="fas fa-check text-green-600 text-3xl"></i>' +
                        '</div>' +
                        '<div class="text-center">' +
                            '<p class="text-xl font-extrabold text-gray-900 mb-1">Pedido realizado! 🎉</p>' +
                            '<p class="text-sm text-gray-500">Pedido <strong>' + data.numero + '</strong> enviado com sucesso.</p>' +
                        '</div>' +
                        '<a href="{{ route('client.orders.index') }}"' +
                        '   class="mt-2 inline-flex items-center gap-2 rounded-xl bg-green-600 text-white px-8 py-3.5 font-semibold hover:bg-green-700 transition-all active:scale-95">' +
                            '<i class="fas fa-list-check"></i> Ver meus pedidos' +
                        '</a>' +
                    '</div>';
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-1.5"></i> Confirmar pedido';
                alert('Erro ao enviar o pedido. Tente novamente.');
            });
        }

        // ── INIT ──────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            recalculate();
            updateDateConstraints();
            validateStep2UI();

            document.getElementById('delivery-date').addEventListener('change', function () {
                state.deliveryDate = this.value;
                validateStep2UI();
            });

            document.getElementById('order-notes').addEventListener('input', function () {
                state.notes = this.value;
            });
        });
    </script>

@endsection
