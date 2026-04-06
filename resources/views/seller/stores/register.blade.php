@extends('layouts.seller')

@section('title', 'Cadastrar Loja')

@section('content')

    <div class="mx-auto max-w-sm mt-4">

        <div class="mb-4">
            <a href="{{ route('seller.stores.index') }}"
               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2 text-xs"></i> Voltar
            </a>
        </div>

        <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-1">Cadastrar Loja</h2>
        <p class="text-sm text-gray-600 mb-8">Digite o CNPJ para verificar e cadastrar uma nova loja.</p>

        {{-- ESTADO INICIAL --}}
        <div id="initial-state">
            <form id="store-form">
                @csrf
                <div class="mb-4">
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                    <input id="cnpj" name="cnpj" type="text" required autofocus
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="XX.XXX.XXX/XXXX-XX" />
                </div>
                <button type="submit" id="submit-btn"
                    class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 shadow-sm transition-colors disabled:opacity-50">
                    Verificar CNPJ
                </button>
            </form>
        </div>

        {{-- LOADING --}}
        <div id="loading-state" class="hidden text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div>
            <p class="text-sm text-gray-500">Verificando CNPJ...</p>
        </div>

        {{-- PASSO 1 - Dados Básicos --}}
        <div id="step1-state" class="hidden">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Passo 1 de 3: Dados Básicos</h3>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-600 h-2 rounded-full" style="width:33%"></div></div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-5 mb-5 space-y-3">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">Razão Social</p>
                    <p id="step1-legal-name" class="text-gray-900 font-medium mt-0.5"></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase">CNPJ</p>
                    <p id="step1-cnpj" class="text-gray-900 font-medium mt-0.5"></p>
                </div>
                <div>
                    <label for="fantasy-name" class="block text-xs font-medium text-gray-500 uppercase mb-1">Nome Fantasia *</label>
                    <input id="fantasy-name" type="text" required placeholder="Ex: Loja Central"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    <p id="fantasy-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
            </div>
            <button id="step1-btn" class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 shadow-sm transition-colors mb-2">Continuar para Endereço</button>
            <button id="step1-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Voltar</button>
        </div>

        {{-- STORE EXISTENTE (sem seller) --}}
        <div id="existing-no-seller-state" class="hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-5">
                <h3 class="text-sm font-medium text-blue-800 mb-1">Loja já cadastrada no sistema</h3>
                <p class="text-sm text-blue-700">Esta loja não possui vendedor. Confirme os dados para assumir o vínculo (pendente de aprovação admin).</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-5 mb-5 space-y-3">
                <div><p class="text-xs font-medium text-gray-500 uppercase">Razão Social</p><p id="exns-legal-name" class="text-gray-900 font-medium mt-0.5"></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase">CNPJ</p><p id="exns-cnpj" class="text-gray-900 font-medium mt-0.5"></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase">Nome Fantasia</p><p id="exns-name" class="text-gray-900 font-medium mt-0.5"></p></div>
            </div>
            <button id="exns-btn" class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 shadow-sm transition-colors mb-2">Solicitar Vínculo</button>
            <button id="exns-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Voltar</button>
        </div>

        {{-- STORE EXISTENTE (já tem seller) — só claim --}}
        <div id="existing-has-seller-state" class="hidden">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-5">
                <h3 class="text-sm font-medium text-amber-800 mb-1">Loja já possui vendedor</h3>
                <p class="text-sm text-amber-700">Você pode enviar uma solicitação para o admin transferir o vínculo para você.</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-5 mb-5 space-y-3">
                <div><p class="text-xs font-medium text-gray-500 uppercase">Razão Social</p><p id="exhs-legal-name" class="text-gray-900 font-medium mt-0.5"></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase">CNPJ</p><p id="exhs-cnpj" class="text-gray-900 font-medium mt-0.5"></p></div>
                <div><p class="text-xs font-medium text-gray-500 uppercase">Nome Fantasia</p><p id="exhs-name" class="text-gray-900 font-medium mt-0.5"></p></div>
            </div>
            <button id="exhs-btn" class="w-full flex justify-center rounded-lg bg-amber-600 px-4 py-3 text-base font-semibold text-white hover:bg-amber-700 shadow-sm transition-colors mb-2">Enviar Solicitação de Aprovação</button>
            <button id="exhs-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Voltar</button>
        </div>

        {{-- PASSO 2 - Endereço --}}
        <div id="step2-state" class="hidden">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Passo 2 de 3: Endereço</h3>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-600 h-2 rounded-full" style="width:66%"></div></div>
            </div>
            <form id="address-form" class="bg-green-50 border border-green-200 rounded-lg p-5 mb-5 space-y-4">
                <div>
                    <label for="address-cep" class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                    <input id="address-cep" type="text" required maxlength="9" placeholder="XXXXX-XXX"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    <p id="address-cep-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div>
                    <label for="address-street" class="block text-sm font-medium text-gray-700 mb-1">Logradouro *</label>
                    <input id="address-street" type="text" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    <p id="address-street-error" class="mt-1 text-sm text-red-600 hidden"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address-number" class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                        <input id="address-number" type="text" required placeholder="123"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                    <div>
                        <label for="address-complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input id="address-complement" type="text" placeholder="Sala 1"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                </div>
                <div>
                    <label for="address-district" class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                    <input id="address-district" type="text" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address-city" class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                        <input id="address-city" type="text" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                    <div>
                        <label for="address-state" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select id="address-state" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">Escolher</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}">{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
            <button id="step2-btn" class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 shadow-sm transition-colors mb-2">Continuar para Horários</button>
            <button id="step2-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Voltar</button>
        </div>

        {{-- PASSO 3 - Horários --}}
        <div id="step3-state" class="hidden">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Passo 3 de 3: Horários de Funcionamento</h3>
                <div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-green-600 h-2 rounded-full" style="width:100%"></div></div>
            </div>

            <div class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4">
                <p class="mb-3 text-sm font-medium text-green-800">Aplicar horário em massa</p>
                <div class="flex items-center gap-2 mb-3">
                    <input type="time" id="reg-bulk-open" value="08:00" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
                    <span class="text-sm text-gray-500">às</span>
                    <input type="time" id="reg-bulk-close" value="18:00" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm" />
                </div>
                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach(['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $i => $d)
                    <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                        <input type="checkbox" class="reg-bulk-day sr-only" data-day="{{ $i }}" /> {{ $d }}
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="regApplyBulkToAll()" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700">Todos</button>
                    <button type="button" onclick="regApplyBulkToSelected()" class="rounded-lg border border-green-600 px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-50">Selecionados</button>
                </div>
            </div>

            <form id="hours-form" class="bg-green-50 border border-green-200 rounded-lg p-5 mb-5 space-y-3">
                @foreach([0=>'Segunda-feira',1=>'Terça-feira',2=>'Quarta-feira',3=>'Quinta-feira',4=>'Sexta-feira',5=>'Sábado',6=>'Domingo'] as $day => $label)
                <div class="{{ $day < 6 ? 'pb-3 border-b border-green-200' : '' }}">
                    <div class="flex items-center justify-between">
                        <label class="font-medium text-gray-900">{{ $label }}</label>
                        <input type="checkbox" data-day="{{ $day }}" class="day-toggle h-4 w-4 text-green-600 rounded" {{ $day < 6 ? 'checked' : '' }} />
                    </div>
                    <div class="day-inputs-{{ $day }} {{ $day === 6 ? 'hidden' : '' }} grid grid-cols-2 gap-2 mt-2">
                        <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm" value="08:00" />
                        <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm" value="18:00" />
                    </div>
                </div>
                @endforeach
            </form>

            <button id="step3-btn" class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 shadow-sm transition-colors mb-2">Cadastrar Loja</button>
            <button id="step3-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Voltar</button>
        </div>

        {{-- SUCESSO --}}
        <div id="success-state" class="hidden text-center py-8">
            <i class="fas fa-check-circle text-green-600 text-5xl mb-4"></i>
            <p id="success-message" class="text-lg font-semibold text-gray-900 mb-2"></p>
            <p class="text-sm text-gray-500 mb-6">Aguarde aprovação do admin para ativar as comissões.</p>
            <a href="{{ route('seller.stores.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">
                Ver minhas lojas
            </a>
        </div>

        {{-- ERRO --}}
        <div id="error-state" class="hidden">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5">
                <p class="text-sm text-red-700" id="error-message"></p>
            </div>
            <button id="error-back-btn" class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 shadow-sm transition-colors">Tentar novamente</button>
        </div>

    </div>

    <script>
        const CSRF = '{{ csrf_token() }}';
        const VERIFY_URL = '{{ route('seller.stores.verify-cnpj') }}';
        const STEP1_URL  = '{{ route('seller.stores.step1') }}';
        const STEP3_URL  = '{{ route('seller.stores.step3') }}';
        const INDEX_URL  = '{{ route('seller.stores.index') }}';

        const states = {
            initial:        document.getElementById('initial-state'),
            loading:        document.getElementById('loading-state'),
            step1:          document.getElementById('step1-state'),
            exNoSeller:     document.getElementById('existing-no-seller-state'),
            exHasSeller:    document.getElementById('existing-has-seller-state'),
            step2:          document.getElementById('step2-state'),
            step3:          document.getElementById('step3-state'),
            success:        document.getElementById('success-state'),
            error:          document.getElementById('error-state'),
        };

        function showState(name) {
            Object.values(states).forEach(s => s.classList.add('hidden'));
            states[name].classList.remove('hidden');
        }

        let flow = { cnpj:'', legal_name:'', name:'', exists_in_database:false, already_has_seller:false, address:{}, hours:{} };

        function headers() {
            return { 'Content-Type':'application/json', 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF };
        }

        async function postJSON(url, body) {
            const r = await fetch(url, { method:'POST', headers: headers(), body: JSON.stringify(body) });
            const data = await r.json().catch(() => ({ message: 'Erro inesperado.' }));
            if (!r.ok) throw new Error(data.message || 'Erro.');
            return data;
        }

        // CNPJ mask
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g,'');
            if (v.length <= 14) {
                if (v.length > 12) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8,12)+'-'+v.slice(12);
                else if (v.length > 8) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5,8)+'/'+v.slice(8);
                else if (v.length > 5) v = v.slice(0,2)+'.'+v.slice(2,5)+'.'+v.slice(5);
                else if (v.length > 2) v = v.slice(0,2)+'.'+v.slice(2);
            }
            e.target.value = v;
        });

        // CEP mask + autofill
        document.getElementById('address-cep').addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g,'');
            if (v.length > 5) v = v.slice(0,5)+'-'+v.slice(5,8);
            e.target.value = v;
        });
        document.getElementById('address-cep').addEventListener('blur', async function() {
            const cep = this.value.replace(/\D/g,'');
            if (cep.length !== 8) return;
            try {
                const r = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const d = await r.json();
                if (d.erro) return;
                document.getElementById('address-street').value = d.logradouro || '';
                document.getElementById('address-district').value = d.bairro || '';
                document.getElementById('address-city').value = d.localidade || '';
                document.getElementById('address-state').value = d.uf || '';
            } catch {}
        });

        // Submit CNPJ
        document.getElementById('store-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const cnpj = document.getElementById('cnpj').value.replace(/\D/g,'');
            if (cnpj.length !== 14) { showState('error'); document.getElementById('error-message').textContent = 'CNPJ inválido.'; return; }
            showState('loading');
            try {
                const data = await postJSON(VERIFY_URL, { cnpj });
                flow.cnpj = data.store.cnpj;
                flow.legal_name = data.store.legal_name;
                flow.name = data.store.name;
                flow.exists_in_database = data.exists_in_database;
                flow.already_has_seller = data.already_has_seller;

                if (!data.exists_in_database) {
                    showState('step1');
                    document.getElementById('step1-legal-name').textContent = flow.legal_name;
                    document.getElementById('step1-cnpj').textContent = formatCNPJ(flow.cnpj);
                    document.getElementById('fantasy-name').value = flow.name;
                } else if (data.already_has_seller) {
                    showState('exHasSeller');
                    document.getElementById('exhs-legal-name').textContent = flow.legal_name;
                    document.getElementById('exhs-cnpj').textContent = formatCNPJ(flow.cnpj);
                    document.getElementById('exhs-name').textContent = flow.name;
                } else {
                    showState('exNoSeller');
                    document.getElementById('exns-legal-name').textContent = flow.legal_name;
                    document.getElementById('exns-cnpj').textContent = formatCNPJ(flow.cnpj);
                    document.getElementById('exns-name').textContent = flow.name;
                }
            } catch(err) {
                showState('error');
                document.getElementById('error-message').textContent = err.message;
            }
        });

        // Step 1 → Step 2
        document.getElementById('step1-btn').addEventListener('click', async function() {
            const name = document.getElementById('fantasy-name').value.trim();
            if (!name) { document.getElementById('fantasy-name-error').classList.remove('hidden'); document.getElementById('fantasy-name-error').textContent='Obrigatório'; return; }
            document.getElementById('fantasy-name-error').classList.add('hidden');
            flow.name = name;
            try {
                await postJSON(STEP1_URL, { cnpj: flow.cnpj, legal_name: flow.legal_name, name: flow.name });
                showState('step2');
            } catch(err) { showState('error'); document.getElementById('error-message').textContent = err.message; }
        });
        document.getElementById('step1-back-btn').addEventListener('click', () => showState('initial'));

        // Existing no-seller → final confirm
        document.getElementById('exns-btn').addEventListener('click', async function() {
            this.disabled=true; this.textContent='Enviando...';
            try {
                const data = await postJSON(STEP3_URL, { cnpj: flow.cnpj, legal_name: flow.legal_name, name: flow.name, exists_in_database: true, already_has_seller: false, address_cep:'00000000', address_street:'N/A', address_number:'N/A', address_district:'N/A', address_city:'N/A', address_state:'SP' });
                showState('success');
                document.getElementById('success-message').textContent = data.message;
            } catch(err) { showState('error'); document.getElementById('error-message').textContent = err.message; }
            finally { this.disabled=false; this.textContent='Solicitar Vínculo'; }
        });
        document.getElementById('exns-back-btn').addEventListener('click', () => showState('initial'));

        // Existing has-seller → send claim
        document.getElementById('exhs-btn').addEventListener('click', async function() {
            this.disabled=true; this.textContent='Enviando...';
            try {
                const data = await postJSON(STEP3_URL, { cnpj: flow.cnpj, legal_name: flow.legal_name, name: flow.name, exists_in_database: true, already_has_seller: true, address_cep:'00000000', address_street:'N/A', address_number:'N/A', address_district:'N/A', address_city:'N/A', address_state:'SP' });
                showState('success');
                document.getElementById('success-message').textContent = data.message;
            } catch(err) { showState('error'); document.getElementById('error-message').textContent = err.message; }
            finally { this.disabled=false; this.textContent='Enviar Solicitação'; }
        });
        document.getElementById('exhs-back-btn').addEventListener('click', () => showState('initial'));

        // Step 2 → Step 3
        document.getElementById('step2-btn').addEventListener('click', function() {
            const cep = document.getElementById('address-cep').value.replace(/\D/g,'');
            const street = document.getElementById('address-street').value.trim();
            const number = document.getElementById('address-number').value.trim();
            const district = document.getElementById('address-district').value.trim();
            const city = document.getElementById('address-city').value.trim();
            const state = document.getElementById('address-state').value.trim();
            if (!cep || cep.length!==8 || !street || !number || !district || !city || !state) {
                document.getElementById('address-cep-error').textContent = cep.length!==8 ? 'CEP inválido' : '';
                document.getElementById('address-cep-error').classList.toggle('hidden', cep.length===8);
                return;
            }
            flow.address = { cep, street, number, complement: document.getElementById('address-complement').value.trim(), district, city, state };
            showState('step3');
        });
        document.getElementById('step2-back-btn').addEventListener('click', () => showState('step1'));

        // Step 3 → Submit
        document.getElementById('step3-btn').addEventListener('click', async function() {
            const hours = {};
            document.querySelectorAll('.day-toggle').forEach(t => {
                const d = t.dataset.day;
                hours[d] = { is_open: t.checked, open_time: t.checked ? document.querySelector(`.day-inputs-${d} .day-open-time`)?.value : null, close_time: t.checked ? document.querySelector(`.day-inputs-${d} .day-close-time`)?.value : null };
            });
            flow.hours = hours;
            this.disabled=true; this.textContent='Cadastrando...';
            try {
                const data = await postJSON(STEP3_URL, {
                    cnpj: flow.cnpj, legal_name: flow.legal_name, name: flow.name,
                    address_cep: flow.address.cep, address_street: flow.address.street, address_number: flow.address.number,
                    address_complement: flow.address.complement, address_district: flow.address.district,
                    address_city: flow.address.city, address_state: flow.address.state,
                    hours: flow.hours, exists_in_database: false, already_has_seller: false
                });
                showState('success');
                document.getElementById('success-message').textContent = data.message;
            } catch(err) { showState('error'); document.getElementById('error-message').textContent = err.message; }
            finally { this.disabled=false; this.textContent='Cadastrar Loja'; }
        });
        document.getElementById('step3-back-btn').addEventListener('click', () => showState('step2'));

        document.getElementById('error-back-btn').addEventListener('click', () => showState('initial'));

        // Day toggle
        document.querySelectorAll('.day-toggle').forEach(t => {
            t.addEventListener('change', function() {
                const d = this.dataset.day;
                const c = document.querySelector(`.day-inputs-${d}`);
                if (this.checked) { c.classList.remove('hidden'); c.classList.add('grid'); }
                else { c.classList.add('hidden'); c.classList.remove('grid'); }
            });
        });

        function regApplyBulkToDay(day, open, close) {
            const t = document.querySelector(`.day-toggle[data-day="${day}"]`); if(!t) return;
            t.checked=true;
            const c = document.querySelector(`.day-inputs-${day}`); c.classList.remove('hidden'); c.classList.add('grid');
            c.querySelector('.day-open-time').value = open;
            c.querySelector('.day-close-time').value = close;
        }
        function regApplyBulkToAll() {
            const o=document.getElementById('reg-bulk-open').value, c=document.getElementById('reg-bulk-close').value;
            for(let i=0;i<=6;i++) regApplyBulkToDay(i,o,c);
        }
        function regApplyBulkToSelected() {
            const o=document.getElementById('reg-bulk-open').value, c=document.getElementById('reg-bulk-close').value;
            document.querySelectorAll('.reg-bulk-day:checked').forEach(cb => regApplyBulkToDay(parseInt(cb.dataset.day),o,c));
        }

        function formatCNPJ(v) {
            const c = v.replace(/\D/g,'');
            return `${c.slice(0,2)}.${c.slice(2,5)}.${c.slice(5,8)}/${c.slice(8,12)}-${c.slice(12)}`;
        }
    </script>

@endsection
