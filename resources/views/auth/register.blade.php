@extends('layouts.auth')

@section('head')
    <title>Criar Conta - Aloha App</title>
@endsection

@section('content')
    <div class="flex min-h-screen flex-col justify-center px-6 py-12 bg-gradient-to-b from-green-50 to-white">

        <div class="mx-auto w-full max-w-sm">
            <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Crie sua conta</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Leva menos de 30 segundos para começar a fazer seus pedidos.
            </p>
        </div>

        <div class="mx-auto w-full max-w-sm mt-8">
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="Seu nome completo" />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="(11) 99999-9999" />
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="seu@email.com" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                            class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 pr-10 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                            placeholder="Mínimo 6 caracteres" />
                        <button type="button" id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                        Criar conta
                    </button>
                    {{-- <p class="mt-2 text-center text-xs text-gray-500">Depois você poderá fazer seu pedido.</p> --}}
                </div>

            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Já possui uma conta?</p>
                <a href="{{ route('login') }}"
                    class="mt-1 inline-block text-sm font-medium text-green-600 hover:text-green-500">Entrar na minha
                    conta</a>
            </div>
        </div>
    </div>

    <script>
        // Phone formatting for Brazil
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function(e) {
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

        // Password toggle
        const toggleButton = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
@endsection
