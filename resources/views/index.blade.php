@extends('layouts.auth')

@section('head')
<title>Aloha App</title>
@endsection

@section('content')
<div class="flex min-h-screen flex-col px-6 py-12 bg-gradient-to-b from-green-50 to-white">

    <div class="flex-1 flex flex-col justify-center">

        <div class="mx-auto w-full max-w-sm">
            <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Bem-vindo ao Aloha App</h2>
            <p class="mt-2 text-center text-sm text-gray-600">Faça seus pedidos de gelo Aloha de forma rápida e fácil.</p>
        </div>

        <div class="mx-auto w-full max-w-sm mt-8">
            <div class="space-y-4">

                <!-- Botão principal -->
                <a href="{{ route('register') }}"
                    class="w-full flex justify-center items-center rounded-lg bg-green-600 px-4 py-3 text-base font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm transition-colors">
                    Criar conta para fazer pedido
                </a>

                <div class="flex items-center">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="px-3 text-xs text-gray-400">ou</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <a href="{{ route('login') }}"
                    class="w-full flex justify-center items-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    Entrar na minha conta
                </a>

            </div>
        </div>

    </div>

</div>
@endsection
