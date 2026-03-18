@extends('layouts.client')

@section('title', 'Meu Perfil')

@section('content')
    @php
        $user = auth()->user();
        $client = $user->client;
        $rawPhone = preg_replace('/\D/', '', $client->phone ?? '');
        $localPhone = (strlen($rawPhone) === 13 && str_starts_with($rawPhone, '55')) ? substr($rawPhone, 2) : $rawPhone;
        if (strlen($localPhone) === 11) {
            $phoneFormatted = '(' . substr($localPhone, 0, 2) . ') ' . substr($localPhone, 2, 5) . '-' . substr($localPhone, 7);
        } elseif (strlen($localPhone) === 10) {
            $phoneFormatted = '(' . substr($localPhone, 0, 2) . ') ' . substr($localPhone, 2, 4) . '-' . substr($localPhone, 6);
        } else {
            $phoneFormatted = $client->phone;
        }
    @endphp

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700 ring-1 ring-green-200">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── HERO ─────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col items-center gap-3 pt-2">
        <img
            src="https://api.dicebear.com/9.x/glass/svg?seed={{ urlencode($user->name) }}"
            alt="Avatar"
            class="h-20 w-20 rounded-full object-cover border-2 border-green-600"
        />

        <div class="text-center">
            <p class="text-xl font-bold text-gray-900">{{ $user->name }}</p>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>

        <button type="button" onclick="openModal('modal-edit-profile')"
            class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
            <i class="fas fa-pen-to-square"></i>
            Editar perfil
        </button>
    </div>

    {{-- ─── INFORMAÇÕES PESSOAIS ─────────────────────────────────── --}}
    <div class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Informações pessoais</h2>
            <button type="button" onclick="openModal('modal-edit-profile')"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-green-500 hover:text-green-600">
                <i class="fas fa-pen text-xs"></i> Editar
            </button>
        </div>

        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Nome</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-envelope text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">E-mail</p>
                    <p class="truncate text-sm font-medium text-gray-900">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                    <i class="fas fa-phone text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500">Telefone</p>
                    <p class="text-sm font-medium text-gray-900">{{ $phoneFormatted }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── SEGURANÇA ────────────────────────────────────────────── --}}
    <div class="mb-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-3 font-semibold text-gray-900">Segurança</h2>

        <div class="space-y-2">
            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                <div class="flex items-center gap-3">
                    <i class="fas fa-lock text-gray-400"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Senha</p>
                        <p class="text-xs text-gray-500">••••••••</p>
                    </div>
                </div>
                <button type="button" onclick="openModal('modal-change-password')"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 shadow-sm transition-colors hover:border-green-500 hover:text-green-600">
                    Alterar
                </button>
            </div>
        </div>
    </div>

    {{-- ─── CONTA ────────────────────────────────────────────────── --}}
    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-black/5">
        <h2 class="mb-3 font-semibold text-gray-900">Conta</h2>

        <div class="space-y-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                    <i class="fas fa-arrow-right-from-bracket w-4 text-gray-400"></i>
                    Sair da conta
                </button>
            </form>

            <button type="button" onclick="openModal('modal-delete-account')"
                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium text-red-500 transition-colors hover:bg-red-50">
                <i class="fas fa-trash-can w-4"></i>
                Excluir conta
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MODAIS                                                         --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}

    {{-- EDITAR PERFIL --}}
    <div id="modal-edit-profile" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Editar perfil</h3>
                <button type="button" onclick="closeModal('modal-edit-profile')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            @if($errors->getBag('edit_profile')->any())
                <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                    <ul class="list-disc pl-3 space-y-0.5">
                        @foreach($errors->getBag('edit_profile')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('client.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Nome</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">E-mail</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Telefone</label>
                        <input type="text" id="phone-edit" name="phone" value="{{ old('phone', $phoneFormatted) }}" required
                            placeholder="(11) 99999-9999"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>
                </div>

                <div class="mt-5 flex gap-3">
                    <button type="button" onclick="closeModal('modal-edit-profile')"
                        class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-green-600 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ALTERAR SENHA --}}
    <div id="modal-change-password" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Alterar senha</h3>
                <button type="button" onclick="closeModal('modal-change-password')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-xmark text-lg"></i>
                </button>
            </div>

            @if($errors->getBag('change_password')->any())
                <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                    <ul class="list-disc pl-3 space-y-0.5">
                        @foreach($errors->getBag('change_password')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('client.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Senha atual</label>
                        <input type="password" name="current_password" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Nova senha</label>
                        <input type="password" name="password" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-700">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                    </div>
                </div>

                <div class="mt-5 flex gap-3">
                    <button type="button" onclick="closeModal('modal-change-password')"
                        class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-green-600 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-green-700">
                        Alterar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- EXCLUIR CONTA --}}
    <div id="modal-delete-account" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl max-h-[90vh] overflow-y-auto">
            <div class="mb-4 flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-triangle-exclamation text-red-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Excluir conta</h3>
                    <p class="text-xs text-gray-500">Essa ação é irreversível</p>
                </div>
            </div>

            <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                <p class="flex items-start gap-2">
                    <i class="fas fa-circle-info mt-0.5 flex-shrink-0"></i>
                    <span>Todos os seus dados serão <strong>permanentemente excluídos</strong> e não poderão ser recuperados.</span>
                </p>
            </div>

            @if($errors->getBag('delete_account')->any())
                <div class="mb-4 flex items-start gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    <i class="fas fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                    <ul class="list-disc pl-3 space-y-0.5">
                        @foreach($errors->getBag('delete_account')->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('client.profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-700">Confirme sua senha para excluir</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>

                <div class="mt-5 flex gap-3">
                    <button type="button" onclick="closeModal('modal-delete-account')"
                        class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 rounded-xl bg-red-500 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-600">
                        Excluir conta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }

        document.querySelectorAll('[id^="modal-"]').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // Máscara de telefone no modal de edição
        const phoneEdit = document.getElementById('phone-edit');
        phoneEdit.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length <= 2) {
                    value = value;
                } else if (value.length <= 6) {
                    value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
                } else if (value.length <= 10) {
                    value = `(${value.slice(0, 2)}) ${value.slice(2, 6)}-${value.slice(6)}`;
                } else {
                    value = `(${value.slice(0, 2)}) ${value.slice(2, 7)}-${value.slice(7)}`;
                }
            }
            e.target.value = value;
        });

        // Reabrir o modal correto ao retornar com erros de validação
        @if($errors->getBag('edit_profile')->any())
            openModal('modal-edit-profile');
        @elseif($errors->getBag('change_password')->any())
            openModal('modal-change-password');
        @elseif($errors->getBag('logout_other')->any())
            openModal('modal-logout-other');
        @elseif($errors->getBag('delete_account')->any())
            openModal('modal-delete-account');
        @endif
    </script>

@endsection
