@extends('layouts.auth')

@section('head')
    <title>Vincular Loja - Aloha App</title>
@endsection

@section('content')
    <div class="flex min-h-screen flex-col justify-center px-6 py-12 bg-gradient-to-b from-green-50 to-white">

        <div class="mx-auto w-full max-w-sm">
            
            <div class="mb-4">
                <a href="{{ route('client.stores.index') }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i>
                    Voltar
                </a>
            </div>

            <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Vincule sua loja</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Para fazer pedidos, você precisa ter pelo menos uma
                loja vinculada à sua conta. Digite o CNPJ para continuar.
            </p>
        </div>

        <div class="mx-auto w-full max-w-sm mt-8">

            <!-- ESTADO INICIAL - Input de CNPJ -->
            <div id="initial-state">
                <form id="store-form" action="{{ route('client.stores.register') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ</label>
                        <input id="cnpj" name="cnpj" type="text" value="{{ old('cnpj') }}" required autofocus
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="XX.XXX.XXX/XXXX-XX" />
                        @error('cnpj')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="submit-btn"
                            class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            Verificar CNPJ
                        </button>
                    </div>
                </form>
            </div>

            <!-- ESTADO DE LOADING -->
            <div id="loading-state" class="hidden">
                <div class="flex justify-center items-center py-6">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div>
                </div>
                <p class="text-center text-sm text-gray-600">Verificando CNPJ...</p>
            </div>

            <!-- PASSO 1 - Dados Básicos -->
            <div id="step1-state" class="hidden">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Passo 1 de 3: Dados Básicos</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 33%"></div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Razão Social</label>
                        <p id="step1-legal-name" class="text-gray-900 font-medium mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">CNPJ</label>
                        <p id="step1-cnpj" class="text-gray-900 font-medium mt-1"></p>
                    </div>

                    <div>
                        <label for="fantasy-name" class="block text-xs font-medium text-gray-500 uppercase mb-1">Nome
                            Fantasia *</label>
                        <input id="fantasy-name" type="text" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="Ex: Loja Central" />
                        <p id="fantasy-name-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>
                </div>

                <button type="button" id="step1-btn"
                    class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors mb-2">
                    Continuar para Endereço
                </button>

                <button type="button" id="step1-back-btn"
                    class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Voltar
                </button>
            </div>

            <!-- ESTADO DE STORE EXISTENTE - Confirmação de dados -->
            <div id="existing-store-state" class="hidden">
                <div class="mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800">Loja já cadastrada no sistema!</h3>
                                <p id="existing-store-message" class="mt-2 text-sm text-blue-700"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Razão Social</label>
                        <p id="existing-legal-name" class="text-gray-900 font-medium mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">CNPJ</label>
                        <p id="existing-cnpj" class="text-gray-900 font-medium mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase">Nome Fantasia</label>
                        <p id="existing-fantasy-name" class="text-gray-900 font-medium mt-1"></p>
                    </div>
                </div>

                <button type="button" id="existing-store-btn"
                    class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors mb-2">
                    Confirmar e Vincular Loja
                </button>

                <button type="button" id="existing-store-back-btn"
                    class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Voltar
                </button>
            </div>

            <!-- PASSO 2 - Endereço -->
            <div id="step2-state" class="hidden">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Passo 2 de 3: Endereço de Entrega</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 66%"></div>
                    </div>
                </div>

                <form id="address-form" class="space-y-4 bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <div>
                        <label for="address-cep" class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                        <input id="address-cep" type="text" required maxlength="9"
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="XXXXX-XXX" />
                        <p id="address-cep-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="address-street" class="block text-sm font-medium text-gray-700 mb-1">Logradouro
                            *</label>
                        <input id="address-street" type="text" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="Ex: Rua das Flores" />
                        <p id="address-street-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="address-number" class="block text-sm font-medium text-gray-700 mb-1">Número
                                *</label>
                            <input id="address-number" type="text" required
                                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                placeholder="123" />
                            <p id="address-number-error" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>

                        <div>
                            <label for="address-complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                            <input id="address-complement" type="text"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                placeholder="Ex: Apto 101" />
                        </div>
                    </div>

                    <div>
                        <label for="address-district" class="block text-sm font-medium text-gray-700 mb-1">Bairro
                            *</label>
                        <input id="address-district" type="text" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="Ex: Centro" />
                        <p id="address-district-error" class="mt-1 text-sm text-red-600 hidden"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="address-city" class="block text-sm font-medium text-gray-700 mb-1">Cidade
                                *</label>
                            <input id="address-city" type="text" required
                                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                                placeholder="Ex: São Paulo" />
                            <p id="address-city-error" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>

                        <div>
                            <label for="address-state" class="block text-sm font-medium text-gray-700 mb-1">Estado
                                *</label>
                            <select id="address-state" required
                                class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 focus:border-green-500 focus:ring-green-500 shadow-sm">
                                <option value="">Escolher estado</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>
                            <p id="address-state-error" class="mt-1 text-sm text-red-600 hidden"></p>
                        </div>
                    </div>
                </form>

                <button type="button" id="step2-btn"
                    class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors mb-2">
                    Continuar para Horários
                </button>

                <button type="button" id="step2-back-btn"
                    class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Voltar
                </button>
            </div>

            <!-- PASSO 3 - Horários -->
            <div id="step3-state" class="hidden">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Passo 3 de 3: Horários de Funcionamento</h3>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Aplicação em massa de horários -->
                <div class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4">
                    <p class="mb-3 text-sm font-medium text-green-800">Aplicar horário em massa</p>

                    <div class="flex items-center gap-2 mb-3">
                        <input type="time" id="reg-bulk-open" value="08:00"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                        <span class="text-sm text-gray-500">às</span>
                        <input type="time" id="reg-bulk-close" value="18:00"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-green-500 focus:ring-green-500" />
                    </div>

                    <div class="mb-3 flex flex-wrap gap-1.5">
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="0" /> Seg
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="1" /> Ter
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="2" /> Qua
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="3" /> Qui
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="4" /> Sex
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="5" /> Sáb
                        </label>
                        <label class="flex cursor-pointer items-center gap-1 rounded-lg border border-green-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 has-[:checked]:border-green-500 has-[:checked]:bg-green-600 has-[:checked]:text-white transition-colors">
                            <input type="checkbox" class="reg-bulk-day sr-only" data-day="6" /> Dom
                        </label>
                    </div>

                    <div class="flex gap-2">
                        <button type="button" onclick="regApplyBulkToAll()"
                            class="rounded-lg bg-green-600 px-3 py-2 text-xs font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                            Aplicar para todos
                        </button>
                        <button type="button" onclick="regApplyBulkToSelected()"
                            class="rounded-lg border border-green-600 px-3 py-2 text-xs font-medium text-green-700 shadow-sm transition-colors hover:bg-green-50">
                            Aplicar para selecionados
                        </button>
                    </div>
                </div>

                <form id="hours-form" class="space-y-3 bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <!-- Segunda -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Segunda-feira</label>
                            <input type="checkbox" data-day="0" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-0 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Terça -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Terça-feira</label>
                            <input type="checkbox" data-day="1" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-1 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Quarta -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Quarta-feira</label>
                            <input type="checkbox" data-day="2" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-2 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Quinta -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Quinta-feira</label>
                            <input type="checkbox" data-day="3" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-3 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Sexta -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Sexta-feira</label>
                            <input type="checkbox" data-day="4" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-4 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Sábado -->
                    <div class="pb-3 border-b border-green-200">
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Sábado</label>
                            <input type="checkbox" data-day="5" class="day-toggle h-4 w-4 text-green-600 rounded"
                                checked />
                        </div>
                        <div class="day-inputs-5 grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>

                    <!-- Domingo -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="font-medium text-gray-900">Domingo</label>
                            <input type="checkbox" data-day="6" class="day-toggle h-4 w-4 text-green-600 rounded" />
                        </div>
                        <div class="day-inputs-6 hidden grid grid-cols-2 gap-2 mt-2">
                            <input type="time" class="day-open-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="08:00" />
                            <input type="time" class="day-close-time rounded-lg border border-gray-300 px-3 py-2 text-sm"
                                value="18:00" />
                        </div>
                    </div>
                </form>

                <button type="button" id="step3-btn"
                    class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors mb-2">
                    Vincular Loja
                </button>

                <button type="button" id="step3-back-btn"
                    class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Voltar
                </button>
            </div>

            <!-- ESTADO DE ERRO -->
            <div id="error-state" class="hidden">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-700" id="error-message"></p>
                </div>
                <button type="button" id="error-back-btn"
                    class="w-full flex justify-center rounded-lg bg-gray-200 px-4 py-3 text-base font-semibold text-gray-900 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Tentar novamente
                </button>
            </div>
        </div>
    </div>

    <script>
        const storeForm = document.getElementById('store-form');
        const cnpjInput = document.getElementById('cnpj');
        const submitBtn = document.getElementById('submit-btn');

        const initialState = document.getElementById('initial-state');
        const loadingState = document.getElementById('loading-state');
        const step1State = document.getElementById('step1-state');
        const existingStoreState = document.getElementById('existing-store-state');
        const step2State = document.getElementById('step2-state');
        const step3State = document.getElementById('step3-state');
        const errorState = document.getElementById('error-state');

        const cepInput = document.getElementById('address-cep');

        cepInput.addEventListener('blur', async function () {
            const cep = cepInput.value.replace(/\D/g, '');

            if (cep.length !== 8) return;

            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (data.erro) {
                    throw new Error('CEP não encontrado');
                }

                // Preencher campos automaticamente
                document.getElementById('address-street').value = data.logradouro || '';
                document.getElementById('address-district').value = data.bairro || '';
                document.getElementById('address-city').value = data.localidade || '';
                document.getElementById('address-state').value = data.uf || '';

                // Salvar no flow também
                currentFlowData.address.cep = cep;
                currentFlowData.address.street = data.logradouro || '';
                currentFlowData.address.district = data.bairro || '';
                currentFlowData.address.city = data.localidade || '';
                currentFlowData.address.state = data.uf || '';

            } catch (error) {
                document.getElementById('address-cep-error').textContent = 'CEP inválido ou não encontrado';
                document.getElementById('address-cep-error').classList.remove('hidden');
            }
        });

        let currentFlowData = {
            cnpj: '',
            legal_name: '',
            name: '',
            exists_in_database: false,
            address: {
                cep: '',
                street: '',
                number: '',
                complement: '',
                district: '',
                city: '',
                state: ''
            },
            hours: {}
        };

        // CNPJ formatting
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                if (value.length <= 2) {
                    value = value;
                } else if (value.length <= 5) {
                    value = `${value.slice(0, 2)}.${value.slice(2)}`;
                } else if (value.length <= 8) {
                    value = `${value.slice(0, 2)}.${value.slice(2, 5)}.${value.slice(5)}`;
                } else {
                    value = `${value.slice(0, 2)}.${value.slice(2, 5)}.${value.slice(5, 8)}/${value.slice(8, 12)}-${value.slice(12)}`;
                }
            }
            e.target.value = value;
        });

        // CEP formatting
        document.getElementById('address-cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                if (value.length <= 5) {
                    value = value;
                } else {
                    value = `${value.slice(0, 5)}-${value.slice(5)}`;
                }
            }
            e.target.value = value;
        });

        // Form submission
        storeForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const cnpj = cnpjInput.value.replace(/\D/g, '');

            if (cnpj.length !== 14) {
                showError('CNPJ inválido. Digite um CNPJ com 14 dígitos.');
                return;
            }

            // Show loading state
            initialState.classList.add('hidden');
            loadingState.classList.remove('hidden');
            submitBtn.disabled = true;

            try {
                const response = await fetch('{{ route('client.stores.verify-cnpj') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ cnpj: cnpj })
                });

                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json') ? await response.json() : {
                    message: 'Erro inesperado no servidor. Tente novamente.'
                };

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao verificar CNPJ');
                }

                currentFlowData.cnpj = data.store.cnpj;
                currentFlowData.legal_name = data.store.legal_name || '';
                currentFlowData.name = data.store.name || '';
                currentFlowData.exists_in_database = data.exists_in_database || false;

                // Se store já existe no banco, mostrar estado específico
                if (data.exists_in_database) {
                    showExistingStore(data.store, data.message);
                } else {
                    showStep1(data.store);
                }

            } catch (error) {
                showError(error.message || 'Erro ao verificar CNPJ. Tente novamente.');
            } finally {
                submitBtn.disabled = false;
            }
        });

        // ESTADO DE STORE EXISTENTE
        function showExistingStore(storeData, message) {
            loadingState.classList.add('hidden');
            existingStoreState.classList.remove('hidden');

            document.getElementById('existing-store-message').textContent = message || 'Encontramos esse CNPJ no nosso sistema! Confirme se os dados estão corretos.';
            document.getElementById('existing-legal-name').textContent = storeData.legal_name || '-';
            document.getElementById('existing-cnpj').textContent = formatCNPJ(storeData.cnpj);
            document.getElementById('existing-fantasy-name').textContent = storeData.name || '-';
        }

        // PASSO 1
        function showStep1(storeData) {
            loadingState.classList.add('hidden');
            step1State.classList.remove('hidden');

            document.getElementById('step1-legal-name').textContent = storeData.legal_name || '-';
            document.getElementById('step1-cnpj').textContent = formatCNPJ(storeData.cnpj);
            document.getElementById('fantasy-name').value = storeData.name || '';
        }

        document.getElementById('step1-btn').addEventListener('click', async function() {
            const fantasyName = document.getElementById('fantasy-name').value.trim();
            const fantasyError = document.getElementById('fantasy-name-error');

            if (!fantasyName) {
                fantasyError.textContent = 'Nome Fantasia é obrigatório';
                fantasyError.classList.remove('hidden');
                return;
            }

            currentFlowData.name = fantasyName;
            fantasyError.classList.add('hidden');

            // Confirmar step1 no backend
            try {
                const response = await fetch('{{ route('client.stores.step1') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        cnpj: currentFlowData.cnpj,
                        legal_name: currentFlowData.legal_name,
                        name: currentFlowData.name,
                        exists_in_database: currentFlowData.exists_in_database
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao confirmar dados');
                }

                showStep2();
            } catch (error) {
                showError(error.message || 'Erro ao confirmar dados. Tente novamente.');
            }
        });

        // Botão para store existente - vai direto para vinculação
        document.getElementById('existing-store-btn').addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Vinculando...';

            try {
                const response = await fetch('{{ route('client.stores.step1') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        cnpj: currentFlowData.cnpj,
                        legal_name: currentFlowData.legal_name,
                        name: currentFlowData.name,
                        exists_in_database: true
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao confirmar dados');
                }

                // Para stores existentes, vincular direto
                await linkExistingStore();

            } catch (error) {
                showError(error.message || 'Erro ao vincular loja. Tente novamente.');
                this.disabled = false;
                this.textContent = 'Confirmar e Vincular Loja';
            }
        });

        // Função para vincular store existente
        async function linkExistingStore() {
            try {
                const response = await fetch('{{ route('client.stores.step3') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        cnpj: currentFlowData.cnpj,
                        legal_name: currentFlowData.legal_name,
                        name: currentFlowData.name,
                        address_cep: '00000000', // Dados dummy para stores existentes
                        address_street: 'N/A',
                        address_number: 'N/A',
                        address_complement: '',
                        address_district: 'N/A',
                        address_city: 'N/A',
                        address_state: 'SP',
                        hours: {
                            "0": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "1": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "2": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "3": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "4": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "5": {"is_open": true, "open_time": "08:00", "close_time": "18:00"},
                            "6": {"is_open": false, "open_time": null, "close_time": null}
                        },
                        exists_in_database: true
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao vincular loja');
                }

                // Redirect to stores index
                window.location.href = '{{ route('client.stores.index') }}';

            } catch (error) {
                throw error; // Propagar erro para o handler do botão
            }
        }

        // PASSO 2
        function showStep2() {
            step1State.classList.add('hidden');
            step2State.classList.remove('hidden');

            // Restaurar dados se existirem
            if (currentFlowData.address.cep) {
                document.getElementById('address-cep').value = currentFlowData.address.cep;
                document.getElementById('address-street').value = currentFlowData.address.street;
                document.getElementById('address-number').value = currentFlowData.address.number;
                document.getElementById('address-complement').value = currentFlowData.address.complement;
                document.getElementById('address-district').value = currentFlowData.address.district;
                document.getElementById('address-city').value = currentFlowData.address.city;
                document.getElementById('address-state').value = currentFlowData.address.state;
            }
        }

        document.getElementById('step2-btn').addEventListener('click', function() {
            const cep = document.getElementById('address-cep').value.replace(/\D/g, '');
            const street = document.getElementById('address-street').value.trim();
            const number = document.getElementById('address-number').value.trim();
            const district = document.getElementById('address-district').value.trim();
            const city = document.getElementById('address-city').value.trim();
            const state = document.getElementById('address-state').value.trim();

            // Limpar erros
            document.getElementById('address-cep-error').classList.add('hidden');
            document.getElementById('address-street-error').classList.add('hidden');
            document.getElementById('address-number-error').classList.add('hidden');
            document.getElementById('address-district-error').classList.add('hidden');
            document.getElementById('address-city-error').classList.add('hidden');
            document.getElementById('address-state-error').classList.add('hidden');

            let hasError = false;

            if (cep.length !== 8) {
                document.getElementById('address-cep-error').textContent = 'CEP inválido';
                document.getElementById('address-cep-error').classList.remove('hidden');
                hasError = true;
            }

            if (!street) {
                document.getElementById('address-street-error').textContent = 'Logradouro obrigatório';
                document.getElementById('address-street-error').classList.remove('hidden');
                hasError = true;
            }

            if (!number) {
                document.getElementById('address-number-error').textContent = 'Número obrigatório';
                document.getElementById('address-number-error').classList.remove('hidden');
                hasError = true;
            }

            if (!district) {
                document.getElementById('address-district-error').textContent = 'Bairro obrigatório';
                document.getElementById('address-district-error').classList.remove('hidden');
                hasError = true;
            }

            if (!city) {
                document.getElementById('address-city-error').textContent = 'Cidade obrigatória';
                document.getElementById('address-city-error').classList.remove('hidden');
                hasError = true;
            }

            if (!state) {
                document.getElementById('address-state-error').textContent = 'Estado obrigatório';
                document.getElementById('address-state-error').classList.remove('hidden');
                hasError = true;
            }

            if (hasError) return;

            currentFlowData.address = {
                cep: cep,
                street: street,
                number: number,
                complement: document.getElementById('address-complement').value.trim(),
                district: district,
                city: city,
                state: state
            };

            showStep3();
        });

        // PASSO 3
        function showStep3() {
            step2State.classList.add('hidden');
            step3State.classList.remove('hidden');
        }

        // Bulk apply helpers for register step3
        function regApplyBulkToDay(day, openTime, closeTime) {
            const toggle = document.querySelector(`.day-toggle[data-day="${day}"]`);
            if (!toggle) return;
            toggle.checked = true;
            const inputsContainer = document.querySelector(`.day-inputs-${day}`);
            inputsContainer.classList.remove('hidden');
            inputsContainer.classList.add('grid');
            const openInput = document.querySelector(`.day-inputs-${day} .day-open-time`);
            const closeInput = document.querySelector(`.day-inputs-${day} .day-close-time`);
            if (openInput) openInput.value = openTime;
            if (closeInput) closeInput.value = closeTime;
        }

        function regApplyBulkToAll() {
            const openTime = document.getElementById('reg-bulk-open').value;
            const closeTime = document.getElementById('reg-bulk-close').value;
            for (let i = 0; i <= 6; i++) {
                regApplyBulkToDay(i, openTime, closeTime);
            }
        }

        function regApplyBulkToSelected() {
            const openTime = document.getElementById('reg-bulk-open').value;
            const closeTime = document.getElementById('reg-bulk-close').value;
            document.querySelectorAll('.reg-bulk-day:checked').forEach(cb => {
                regApplyBulkToDay(parseInt(cb.dataset.day), openTime, closeTime);
            });
        }

        // Toggle para habilitar/desabilitar horários
        document.querySelectorAll('.day-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const day = this.dataset.day;
                const inputsContainer = document.querySelector(`.day-inputs-${day}`);

                if (this.checked) {
                    inputsContainer.classList.remove('hidden');
                    inputsContainer.classList.add('grid');
                } else {
                    inputsContainer.classList.add('hidden');
                    inputsContainer.classList.remove('grid');
                }
            });
        });

        document.getElementById('step3-btn').addEventListener('click', async function() {
            const hoursData = {};

            document.querySelectorAll('.day-toggle').forEach(toggle => {
                const day = toggle.dataset.day;
                const isOpen = toggle.checked;
                const openTime = document.querySelector(`.day-inputs-${day} .day-open-time`)?.value || '';
                const closeTime = document.querySelector(`.day-inputs-${day} .day-close-time`)?.value || '';

                hoursData[day] = {
                    is_open: isOpen,
                    open_time: isOpen ? openTime : null,
                    close_time: isOpen ? closeTime : null
                };
            });

            currentFlowData.hours = hoursData;

            this.disabled = true;
            this.textContent = 'Vinculando...';

            try {
                const response = await fetch('{{ route('client.stores.step3') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        cnpj: currentFlowData.cnpj,
                        legal_name: currentFlowData.legal_name,
                        name: currentFlowData.name,
                        address_cep: currentFlowData.address.cep,
                        address_street: currentFlowData.address.street,
                        address_number: currentFlowData.address.number,
                        address_complement: currentFlowData.address.complement,
                        address_district: currentFlowData.address.district,
                        address_city: currentFlowData.address.city,
                        address_state: currentFlowData.address.state,
                        hours: currentFlowData.hours,
                        exists_in_database: currentFlowData.exists_in_database
                    })
                });

                const contentType = response.headers.get('content-type') || '';
                const data = contentType.includes('application/json') ? await response.json() : {
                    message: 'Erro inesperado no servidor. Tente novamente.'
                };

                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao vincular loja');
                }

                // Redirect to stores index
                window.location.href = '{{ route('client.stores.index') }}';

            } catch (error) {
                showError(error.message || 'Erro ao vincular loja. Tente novamente.');
                this.disabled = false;
                this.textContent = 'Vincular Loja';
            }
        });

        // Botões de voltar
        document.getElementById('step1-back-btn').addEventListener('click', function() {
            step1State.classList.add('hidden');
            initialState.classList.remove('hidden');
            cnpjInput.focus();
        });

        document.getElementById('existing-store-back-btn').addEventListener('click', function() {
            existingStoreState.classList.add('hidden');
            initialState.classList.remove('hidden');
            cnpjInput.focus();
        });

        document.getElementById('step2-back-btn').addEventListener('click', function() {
            step2State.classList.add('hidden');
            step1State.classList.remove('hidden');
        });

        document.getElementById('step3-back-btn').addEventListener('click', function() {
            step3State.classList.add('hidden');
            step2State.classList.remove('hidden');
        });

        // Funções auxiliares
        function showError(message) {
            step1State.classList.add('hidden');
            existingStoreState.classList.add('hidden');
            step2State.classList.add('hidden');
            step3State.classList.add('hidden');
            loadingState.classList.add('hidden');
            errorState.classList.remove('hidden');
            document.getElementById('error-message').textContent = message;
        }

        document.getElementById('error-back-btn').addEventListener('click', function() {
            errorState.classList.add('hidden');
            initialState.classList.remove('hidden');
            cnpjInput.value = '';
            cnpjInput.focus();
            currentFlowData = {
                cnpj: '',
                legal_name: '',
                name: '',
                exists_in_database: false,
                address: {
                    cep: '',
                    street: '',
                    number: '',
                    complement: '',
                    district: '',
                    city: '',
                    state: ''
                },
                hours: {}
            };
        });

        function formatCNPJ(cnpj) {
            const cleaned = cnpj.replace(/\D/g, '');
            return `${cleaned.slice(0, 2)}.${cleaned.slice(2, 5)}.${cleaned.slice(5, 8)}/${cleaned.slice(8, 12)}-${cleaned.slice(12)}`;
        }
    </script>
@endsection
