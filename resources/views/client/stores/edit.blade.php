@extends('layouts.client')

@section('title', 'Editar ' . $store->name)

@section('content')
    @php
        // day_of_week: 0=Seg, 1=Ter, 2=Qua, 3=Qui, 4=Sex, 5=Sáb, 6=Dom (matches register form)
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        $states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
    @endphp

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('client.stores.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-white p-2 text-gray-500 shadow-sm transition-colors hover:border-green-500 hover:text-green-600">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Editar Loja</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
            <p class="font-medium mb-1">Corrija os erros abaixo:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('client.stores.update', $store) }}" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Informações Básicas --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Informações Básicas</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                    <input type="text" value="{{ $store->cnpj }}" disabled
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed" />
                    <p class="mt-1 text-xs text-gray-400">O CNPJ não pode ser alterado.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Razão Social</label>
                    <input type="text" value="{{ $store->legal_name }}" disabled
                        class="block w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-base text-gray-500 cursor-not-allowed" />
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Fantasia</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $store->name) }}" required
                        class="block w-full rounded-lg border {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Endereço --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Endereço</h2>

            <div class="space-y-4">
                <div>
                    <label for="address_cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                    <div class="relative">
                        <input id="address_cep" name="address_cep" type="text"
                            value="{{ old('address_cep', $store->address_cep) }}"
                            placeholder="00000-000" maxlength="9" required
                            class="block w-full rounded-lg border {{ $errors->has('address_cep') ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm pr-10" />
                        <div id="cep-spinner" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                            <i class="fas fa-circle-notch fa-spin text-green-600 text-sm"></i>
                        </div>
                    </div>
                    @error('address_cep')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address_street" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input id="address_street" name="address_street" type="text"
                        value="{{ old('address_street', $store->address_street) }}"
                        placeholder="Rua, Avenida, etc." required
                        class="block w-full rounded-lg border {{ $errors->has('address_street') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm" />
                    @error('address_street')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-5 gap-3">
                    <div class="col-span-2">
                        <label for="address_number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input id="address_number" name="address_number" type="text"
                            value="{{ old('address_number', $store->address_number) }}"
                            placeholder="123" required
                            class="block w-full rounded-lg border {{ $errors->has('address_number') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm" />
                        @error('address_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-3">
                        <label for="address_complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input id="address_complement" name="address_complement" type="text"
                            value="{{ old('address_complement', $store->address_complement) }}"
                            placeholder="Apto, Sala... (opcional)"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    </div>
                </div>

                <div>
                    <label for="address_district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input id="address_district" name="address_district" type="text"
                        value="{{ old('address_district', $store->address_district) }}"
                        placeholder="Bairro" required
                        class="block w-full rounded-lg border {{ $errors->has('address_district') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm" />
                    @error('address_district')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label for="address_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input id="address_city" name="address_city" type="text"
                            value="{{ old('address_city', $store->address_city) }}"
                            placeholder="Cidade" required
                            class="block w-full rounded-lg border {{ $errors->has('address_city') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 shadow-sm" />
                        @error('address_city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="address_state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="address_state" name="address_state" required
                            class="block w-full rounded-lg border {{ $errors->has('address_state') ? 'border-red-400' : 'border-gray-300 focus:border-green-500 focus:ring-green-500' }} bg-white px-3 py-3 text-base text-gray-900 shadow-sm">
                            <option value="">UF</option>
                            @foreach ($states as $uf)
                                <option value="{{ $uf }}" {{ old('address_state', $store->address_state) === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                        @error('address_state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Horários --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-black/5">
            <h2 class="mb-4 text-base font-semibold text-gray-900">Horário de Funcionamento</h2>

            {{-- Bulk apply --}}
            <div class="mb-5 rounded-lg bg-green-50 p-4 ring-1 ring-green-100">
                <p class="mb-3 text-sm font-medium text-green-800">Aplicar horário em massa</p>

                <div class="flex items-center gap-2 mb-3">
                    <input type="time" id="bulk-open" value="08:00"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    <span class="text-sm text-gray-500">às</span>
                    <input type="time" id="bulk-close" value="18:00"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>

                <div class="mb-3 flex flex-wrap gap-1.5">
                    @foreach ($days as $i => $dayLabel)
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="bulk-day sr-only" data-day="{{ $i }}" />
                            {{ $dayLabel }}
                        </label>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="applyBulkToAll()"
                        class="rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                        Aplicar para todos
                    </button>
                    <button type="button" onclick="applyBulkToSelected()"
                        class="rounded-lg border border-green-600 px-3 py-2 text-xs font-medium text-green-700 shadow-sm transition-colors hover:bg-green-50">
                        Aplicar para selecionados
                    </button>
                </div>
            </div>

            {{-- Per-day rows --}}
            <div class="space-y-1">
                @for ($i = 0; $i <= 6; $i++)
                    @php
                        $hour     = $storeHours[$i] ?? null;
                        $isOpen   = $hour ? (bool) $hour->is_open : ($i <= 4); // 0-4=Seg-Sex open by default
                        $openTime = old("hours.$i.open_time", $hour && $hour->open_time ? substr($hour->open_time, 0, 5) : '08:00');
                        $closeTime = old("hours.$i.close_time", $hour && $hour->close_time ? substr($hour->close_time, 0, 5) : '18:00');
                        $isOpenOld = old("hours.$i.is_open");
                        if ($isOpenOld !== null) {
                            $isOpen = $isOpenOld === '1';
                        }
                    @endphp

                    <div class="flex items-center gap-3 rounded-lg px-2 py-2.5 transition-colors hover:bg-gray-50" id="day-row-{{ $i }}">
                        <span class="w-8 flex-shrink-0 text-sm font-medium text-gray-700">{{ $days[$i] }}</span>

                        {{-- Hidden fallback so unchecked = 0 --}}
                        <input type="hidden" name="hours[{{ $i }}][is_open]" value="0">
                        <input type="checkbox" name="hours[{{ $i }}][is_open]" value="1"
                            id="day-open-{{ $i }}"
                            {{ $isOpen ? 'checked' : '' }}
                            onchange="toggleDay({{ $i }}, this.checked)"
                            class="h-4 w-4 flex-shrink-0 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer" />

                        <div id="day-times-{{ $i }}"
                            class="flex flex-1 items-center gap-1.5 transition-opacity {{ !$isOpen ? 'pointer-events-none opacity-30' : '' }}">
                            <input type="time" name="hours[{{ $i }}][open_time]"
                                value="{{ $openTime }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                            <span class="flex-shrink-0 text-xs text-gray-400">às</span>
                            <input type="time" name="hours[{{ $i }}][close_time]"
                                value="{{ $closeTime }}"
                                class="w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 py-3.5 text-base font-semibold text-white shadow-sm transition-colors hover:bg-green-700 active:bg-green-800">
            <i class="fas fa-check"></i>
            Salvar Alterações
        </button>
    </form>

    <script>
        // Toggle time inputs for a single day
        function toggleDay(day, isOpen) {
            const times = document.getElementById('day-times-' + day);
            times.classList.toggle('opacity-30', !isOpen);
            times.classList.toggle('pointer-events-none', !isOpen);
        }

        function applyBulkToDay(day, openTime, closeTime) {
            document.getElementById('day-open-' + day).checked = true;
            document.querySelector(`input[name="hours[${day}][open_time]"]`).value = openTime;
            document.querySelector(`input[name="hours[${day}][close_time]"]`).value = closeTime;
            toggleDay(day, true);
        }

        function applyBulkToAll() {
            const openTime = document.getElementById('bulk-open').value;
            const closeTime = document.getElementById('bulk-close').value;
            for (let i = 0; i <= 6; i++) {
                applyBulkToDay(i, openTime, closeTime);
            }
        }

        function applyBulkToSelected() {
            const openTime = document.getElementById('bulk-open').value;
            const closeTime = document.getElementById('bulk-close').value;
            document.querySelectorAll('.bulk-day:checked').forEach(cb => {
                applyBulkToDay(parseInt(cb.dataset.day), openTime, closeTime);
            });
        }

        // CEP auto-fill via ViaCEP
        document.getElementById('address_cep').addEventListener('blur', async function () {
            const raw = this.value.replace(/\D/g, '');
            if (raw.length !== 8) return;

            document.getElementById('cep-spinner').classList.remove('hidden');

            try {
                const res = await fetch(`https://viacep.com.br/ws/${raw}/json/`);
                const data = await res.json();

                if (!data.erro) {
                    document.getElementById('address_street').value   = data.logradouro || '';
                    document.getElementById('address_district').value = data.bairro || '';
                    document.getElementById('address_city').value     = data.localidade || '';
                    document.getElementById('address_state').value    = data.uf || '';
                }
            } catch (_) {
                // silent fail — user can fill manually
            } finally {
                document.getElementById('cep-spinner').classList.add('hidden');
            }
        });

        // Format CEP input as XXXXX-XXX
        document.getElementById('address_cep').addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5);
            this.value = v;
        });
    </script>
@endsection
