@extends('layouts.app')

@section('head')
    <title>@yield('title') - Aloha App</title>
@endsection

@section('body')
    <body class="min-h-screen mx-auto w-full max-w-lg bg-gradient-to-b from-green-50 to-white text-gray-900 px-4 pt-20 pb-6">

        @php $clientStores = auth()->user()->client->stores; @endphp

        <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm ring-1 ring-black/5">
            <div class="mx-auto flex w-full max-w-lg items-center justify-between px-4 py-3 md:max-w-2xl">

                <span class="flex items-center gap-2 text-xl font-bold text-black-700 p-0 m-0">
                    <img src="{{ asset('favicon.ico') }}" alt="Aloha" class="w-8 h-8">
                    Aloha App
                </span>

                <form method="POST" action="{{ route('client.set.store') }}" class="max-w-[160px]">
                    @csrf
                    <select name="store_id" onchange="this.form.submit()"
                        class="w-full truncate rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-bold text-gray-700 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">

                        @foreach($clientStores as $store)
                            <option value="{{ $store->id }}" {{ session('store_id') == $store->id ? 'selected' : '' }}>
                                {{ \Illuminate\Support\Str::limit($store->name, 20) }}
                            </option>
                        @endforeach

                    </select>
                </form>

            </div>
        </header>

        <div class="min-h-screen pb-28">
            @yield('content')
        </div>

        @include('client.partials.navbar')
    </body>
@endsection
