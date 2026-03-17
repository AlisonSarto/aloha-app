@extends('layouts.app')

@section('head')
    <title>@yield('title') - Aloha App</title>
@endsection

@section('body')
    <body class="min-h-screen bg-gradient-to-b from-green-50 to-white text-gray-900">
        <div class="min-h-screen pb-28">
            @yield('content')
        </div>

        @include('client.partials.navbar')
    </body>
@endsection
