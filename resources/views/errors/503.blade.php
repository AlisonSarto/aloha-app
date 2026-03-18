@extends('layouts.app')

@section('head')
    <title>Em manutenção - Aloha App</title>
@endsection

@section('body')

    <body class='h-full'>

        <div class="flex flex-col items-center justify-center min-h-screen text-center bg-gradient-to-b from-green-50 to-white">

            <div class="mx-auto w-full max-w-sm">
                <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-16 w-auto mb-4" />
            </div>

            <h2 class="text-center text-2xl font-bold tracking-tight text-gray-900">Estamos passando por uma atualização</h2>

            <p class="mt-4 text-lg text-gray-600">Em breve o Aloha App voltara a funcionar</p>

        </div>

    </body>
@endsection
