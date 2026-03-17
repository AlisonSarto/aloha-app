@extends('layouts.auth')

@section('head')
    <title>Entrar - Aloha App</title>
@endsection

@section('content')
    <div class="flex min-h-screen flex-col justify-center px-6 py-12 bg-gradient-to-b from-green-50 to-white">

        <div class="mx-auto w-full max-w-sm">
            <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Entre na sua conta</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Acesse sua conta para fazer seus pedidos.</p>
        </div>

        <div class="mx-auto w-full max-w-sm mt-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="seu@email.com" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                    <input id="password" name="password" type="password" required
                        class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-base text-gray-900 placeholder-gray-400 focus:border-green-500 focus:ring-green-500 shadow-sm"
                        placeholder="Sua senha" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('error')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full flex justify-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                        Entrar
                    </button>
                </div>

            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Ainda não tem conta?</p>
                <a href="{{ route('register') }}"
                    class="mt-1 inline-block text-sm font-medium text-green-600 hover:text-green-500">Criar minha conta</a>
            </div>
        </div>
    </div>
@endsection
