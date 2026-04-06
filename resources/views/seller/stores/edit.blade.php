@extends('layouts.seller')

@section('title', 'Editar ' . $store->name)

@section('content')
    @php
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $ufs  = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
    @endphp

    <div class="flex items-center gap-3 mb-6 mt-4">
        <a href="{{ route('seller.stores.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm transition-colors hover:border-green-500 hover:text-green-600">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Editar Loja</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('seller.stores.update', $store) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Informações Básicas</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                    <input type="text" value="{{ $store->cnpj }}" disabled
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                    <input type="text" value="{{ $store->legal_name }}" disabled
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed" />
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $store->name) }}" required
                        class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base shadow-sm" />
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Endereço</h2>
            <div class="space-y-4">
                <div>
                    <label for="address_cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <div class="relative">
                        <input id="address_cep" name="address_cep" type="text"
                            value="{{ old('address_cep', $store->address_cep) }}"
                            placeholder="00000-000" maxlength="9" required
                            class="block w-full rounded-lg border {{ $errors->has('address_cep') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base shadow-sm pr-10" />
                        <div id="cep-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                            <i class="fas fa-circle-notch fa-spin text-green-600 text-sm"></i>
                        </div>
                    </div>
                    @error('address_cep')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="address_street" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input id="address_street" name="address_street" type="text" value="{{ old('address_street', $store->address_street) }}" required
                        class="block w-full rounded-lg border {{ $errors->has('address_street') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base shadow-sm" />
                    @error('address_street')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-2">
                        <label for="address_number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input id="address_number" name="address_number" type="text" value="{{ old('address_number', $store->address_number) }}" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                    <div class="col-span-3">
                        <label for="address_complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input id="address_complement" name="address_complement" type="text" value="{{ old('address_complement', $store->address_complement) }}"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                </div>
                <div>
                    <label for="address_district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input id="address_district" name="address_district" type="text" value="{{ old('address_district', $store->address_district) }}" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label for="address_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input id="address_city" name="address_city" type="text" value="{{ old('address_city', $store->address_city) }}" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    </div>
                    <div>
                        <label for="address_state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="address_state" name="address_state" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-3 text-base shadow-sm focus:border-green-500 focus:ring-green-500">
                            <option value="">UF</option>
                            @foreach($ufs as $uf)
                                <option value="{{ $uf }}" {{ old('address_state', $store->address_state) === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Horário de Funcionamento</h2>
            <div class="mb-4 rounded-lg bg-green-50 p-4 ring-1 ring-green-100">
                <p class="mb-3 text-sm font-medium text-green-800">Aplicar em massa</p>
                <div class="flex items-center gap-2 mb-3">
                    <input type="time" id="bulk-open" value="08:00" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"/>
                    <span class="text-sm text-gray-500">às</span>
                    <input type="time" id="bulk-close" value="18:00" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500"/>
                </div>
                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach($days as $i => $d)
                    <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                        <input type="checkbox" class="bulk-day sr-only" data-day="{{ $i }}" /> {{ $d }}
                    </label>
                    @endforeach
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="applyBulkToAll()" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white hover:bg-green-700">Todos</button>
                    <button type="button" onclick="applyBulkToSelected()" class="rounded-lg border border-green-600 px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-50">Selecionados</button>
                </div>
            </div>
            <div class="space-y-1">
                @for ($i = 0; $i <= 6; $i++)
                    @php
                        $hour      = $storeHours[$i] ?? null;
                        $isOpen    = $hour ? (bool) $hour->is_open : ($i <= 4);
                        $openTime  = old("hours.$i.open_time",  $hour?->open_time  ? substr($hour->open_time,  0, 5) : '08:00');
                        $closeTime = old("hours.$i.close_time", $hour?->close_time ? substr($hour->close_time, 0, 5) : '18:00');
                        $isOpenOld = old("hours.$i.is_open");
                        if ($isOpenOld !== null) $isOpen = $isOpenOld === '1';
                    @endphp
                    <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 hover:bg-gray-50" id="day-row-{{ $i }}">
                        <span class="w-8 flex-shrink-0 text-sm font-medium text-gray-700">{{ $days[$i] }}</span>
                        <input type="hidden" name="hours[{{ $i }}][is_open]" value="0">
                        <input type="checkbox" name="hours[{{ $i }}][is_open]" value="1" id="day-open-{{ $i }}"
                            {{ $isOpen ? 'checked' : '' }} onchange="toggleDay({{ $i }}, this.checked)"
                            class="h-4 w-4 rounded border-gray-300 text-green-600 cursor-pointer"/>
                        <div id="day-times-{{ $i }}" class="flex flex-1 items-center gap-1.5 {{ !$isOpen ? 'opacity-30 pointer-events-none' : '' }}">
                            <input type="time" name="hours[{{ $i }}][open_time]" value="{{ $openTime }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-green-500 focus:ring-green-500"/>
                            <span class="flex-shrink-0 text-xs text-gray-400">às</span>
                            <input type="time" name="hours[{{ $i }}][close_time]" value="{{ $closeTime }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm focus:border-green-500 focus:ring-green-500"/>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 py-3.5 text-base font-semibold text-white shadow-sm hover:bg-green-700 transition-colors">
            <i class="fas fa-check"></i> Salvar Alterações
        </button>
    </form>

    <script>
        function toggleDay(day, isOpen) {
            const t = document.getElementById('day-times-'+day);
            t.classList.toggle('opacity-30', !isOpen);
            t.classList.toggle('pointer-events-none', !isOpen);
        }
        function applyBulkToDay(day, open, close) {
            document.getElementById('day-open-'+day).checked = true;
            document.querySelector(`input[name="hours[${day}][open_time]"]`).value = open;
            document.querySelector(`input[name="hours[${day}][close_time]"]`).value = close;
            toggleDay(day, true);
        }
        function applyBulkToAll() {
            const o=document.getElementById('bulk-open').value, c=document.getElementById('bulk-close').value;
            for(let i=0;i<=6;i++) applyBulkToDay(i,o,c);
        }
        function applyBulkToSelected() {
            const o=document.getElementById('bulk-open').value, c=document.getElementById('bulk-close').value;
            document.querySelectorAll('.bulk-day:checked').forEach(cb => applyBulkToDay(parseInt(cb.dataset.day),o,c));
        }
        document.getElementById('address_cep').addEventListener('blur', async function() {
            const raw = this.value.replace(/\D/g,'');
            if (raw.length !== 8) return;
            document.getElementById('cep-spinner').classList.remove('hidden');
            try {
                const d = await (await fetch(`https://viacep.com.br/ws/${raw}/json/`)).json();
                if (!d.erro) {
                    document.getElementById('address_street').value = d.logradouro || '';
                    document.getElementById('address_district').value = d.bairro || '';
                    document.getElementById('address_city').value = d.localidade || '';
                    document.getElementById('address_state').value = d.uf || '';
                }
            } catch {} finally { document.getElementById('cep-spinner').classList.add('hidden'); }
        });
        document.getElementById('address_cep').addEventListener('input', function() {
            let v = this.value.replace(/\D/g,'').slice(0,8);
            if (v.length > 5) v = v.slice(0,5)+'-'+v.slice(5);
            this.value = v;
        });
    </script>
@endsection
