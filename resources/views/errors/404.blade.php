@extends('layouts.app')

@section('head')
    <title>Página não encontrada - Aloha App</title>
@endsection

@section('body')

    <body class='h-full'>

        <div class="flex flex-col items-center justify-center min-h-screen text-center bg-gradient-to-b from-green-50 to-white">

            <div class="mx-auto w-full max-w-sm">
                <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            </div>

            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Página não encontrada</h2>
            <h1 class="text-6xl font-bold text-gray-800">404</h1>
            <p class="mt-4 text-lg text-gray-600">O link que você tentou acessar não existe</p>

            <a href="{{ route('home') }}" class="mt-6 px-4 py-2 bg-green-600 text-white rounded-lg">
                Voltar para o início
            </a>
        </div>

    </body>
@endsection
