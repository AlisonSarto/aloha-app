@extends('layouts.admin')

@section('title', 'Criar Unidade')

@section('content')

    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Criar Unidade</h1>
            <p class="text-sm text-gray-500 mt-0.5">Cadastre uma nova unidade e selecione os módulos disponíveis.</p>
        </div>
        <a href="{{ route('admin.tenants.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left text-xs"></i> Voltar
        </a>
    </div>

    <form method="POST" action="{{ route('admin.tenants.store') }}">
        @csrf

        <div class="space-y-5">

            {{-- Identificação --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6 space-y-5">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3">Identificação</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nome da unidade <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm text-gray-900 focus:border-green-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-green-500 transition">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Módulos --}}
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-6">
                <h2 class="font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4">Módulos</h2>

                @php
                    $factoryChecked = in_array('factory', old('groups', []));
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Core (obrigatório) --}}
                    @php $core = $groups['core']; @endphp

                    {{-- Core group always submitted --}}
                    <input type="hidden" name="groups[]" value="core">

                    <div class="rounded-xl border-2 border-green-500 bg-green-50 px-4 py-4 flex flex-col gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-green-100 text-green-600">
                                <i class="fas {{ $core['icon'] }} text-base"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-green-800">{{ $core['label'] }}</p>
                                <p class="text-xs text-green-600 mt-0.5">Obrigatório</p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-current bg-current flex items-center justify-center shrink-0">
                                <i class="fas fa-check text-[10px] text-white"></i>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 pt-1 border-t border-green-200">
                            @foreach ($core['permissions'] as $key => $module)
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-green-200 px-2.5 py-1 text-xs font-medium text-green-700">
                                    <i class="fas {{ $module['icon'] }} text-[10px]"></i>
                                    {{ $module['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Fábrica (opcional) --}}
                    @php $factory = $groups['factory']; @endphp

                    <label id="factory-card"
                        class="rounded-xl border-2 px-4 py-4 flex flex-col gap-3 cursor-pointer transition
                               {{ $factoryChecked ? 'border-orange-500 bg-orange-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300 hover:bg-white' }}">

                        <input type="checkbox" name="groups[]" value="factory" id="factory-checkbox"
                            {{ $factoryChecked ? 'checked' : '' }} class="sr-only">

                        {{-- No individual module inputs needed --}}

                        <div class="flex items-center gap-3">
                            <div id="factory-icon-box"
                                class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                       {{ $factoryChecked ? 'bg-orange-100 text-orange-600' : 'bg-gray-200 text-gray-400' }}">
                                <i class="fas {{ $factory['icon'] }} text-base"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p id="factory-title" class="text-sm font-semibold {{ $factoryChecked ? 'text-orange-800' : 'text-gray-700' }}">
                                    {{ $factory['label'] }}
                                </p>
                                <p id="factory-sub" class="text-xs mt-0.5 {{ $factoryChecked ? 'text-orange-600' : 'text-gray-400' }}">
                                    {{ $factoryChecked ? 'Incluído' : 'Opcional' }}
                                </p>
                            </div>
                            <div id="factory-check"
                                class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition
                                       {{ $factoryChecked ? 'border-orange-500 bg-orange-500' : 'border-gray-300' }}">
                                <i id="factory-check-icon" class="fas fa-check text-[10px] text-white {{ $factoryChecked ? '' : 'opacity-0' }}"></i>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-1 border-t {{ $factoryChecked ? 'border-orange-200' : 'border-gray-200' }}" id="factory-modules-row">
                            @foreach ($factory['permissions'] as $key => $module)
                                <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium
                                             {{ $factoryChecked ? 'bg-white border-orange-200 text-orange-700' : 'bg-white border-gray-200 text-gray-400' }}"
                                    data-factory-chip>
                                    <i class="fas {{ $module['icon'] }} text-[10px]"></i>
                                    {{ $module['name'] }}
                                </span>
                            @endforeach
                        </div>
                    </label>

                </div>

                @error('modules') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
                <i class="fas fa-building text-xs"></i> Criar Unidade
            </button>
            <a href="{{ route('admin.tenants.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                Cancelar
            </a>
        </div>
    </form>

    <script>
        (function () {
            var card       = document.getElementById('factory-card');
            var checkbox   = document.getElementById('factory-checkbox');
            var iconBox    = document.getElementById('factory-icon-box');
            var title      = document.getElementById('factory-title');
            var sub        = document.getElementById('factory-sub');
            var check      = document.getElementById('factory-check');
            var checkIcon  = document.getElementById('factory-check-icon');
            var modulesRow = document.getElementById('factory-modules-row');
            var chips      = document.querySelectorAll('[data-factory-chip]');

            card.addEventListener('click', function () {
                var on = !checkbox.checked;
                checkbox.checked = on;

                if (on) {
                    card.className      = card.className.replace('border-gray-200 bg-gray-50 hover:border-gray-300 hover:bg-white', 'border-orange-500 bg-orange-50');
                    iconBox.className   = iconBox.className.replace('bg-gray-200 text-gray-400', 'bg-orange-100 text-orange-600');
                    title.className     = title.className.replace('text-gray-700', 'text-orange-800');
                    sub.className       = sub.className.replace('text-gray-400', 'text-orange-600');
                    sub.textContent     = 'Incluído';
                    check.className     = check.className.replace('border-gray-300', 'border-orange-500 bg-orange-500');
                    checkIcon.classList.remove('opacity-0');
                    modulesRow.className = modulesRow.className.replace('border-gray-200', 'border-orange-200');
                    chips.forEach(function (c) {
                        c.className = c.className.replace('border-gray-200 text-gray-400', 'border-orange-200 text-orange-700');
                    });
                } else {
                    card.className      = card.className.replace('border-orange-500 bg-orange-50', 'border-gray-200 bg-gray-50 hover:border-gray-300 hover:bg-white');
                    iconBox.className   = iconBox.className.replace('bg-orange-100 text-orange-600', 'bg-gray-200 text-gray-400');
                    title.className     = title.className.replace('text-orange-800', 'text-gray-700');
                    sub.className       = sub.className.replace('text-orange-600', 'text-gray-400');
                    sub.textContent     = 'Opcional';
                    check.className     = check.className.replace('border-orange-500 bg-orange-500', 'border-gray-300');
                    checkIcon.classList.add('opacity-0');
                    modulesRow.className = modulesRow.className.replace('border-orange-200', 'border-gray-200');
                    chips.forEach(function (c) {
                        c.className = c.className.replace('border-orange-200 text-orange-700', 'border-gray-200 text-gray-400');
                    });
                }
            });
        })();
    </script>

@endsection
