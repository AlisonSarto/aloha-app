@extends('layouts.app')

@section('head')
    <title>@yield('title') - Aloha App</title>
@endsection

@section('body')
    <body class="min-h-screen bg-gradient-to-b from-green-50 to-white text-gray-900">

        @if(session('store_id'))
        @php $clientStores = auth()->user()->client->stores; @endphp
        <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm ring-1 ring-black/5">
            <div class="mx-auto flex w-full max-w-lg items-center justify-between px-4 py-3 md:max-w-2xl">

                <span class="text-sm font-bold text-green-700">Aloha App</span>

                @if($clientStores->count() > 1)
                    <form method="POST" action="{{ route('client.set.store') }}">
                        @csrf
                        <select name="store_id" onchange="this.form.submit()"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                            @foreach($clientStores as $store)
                                <option value="{{ $store->id }}" {{ session('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <span class="text-sm font-medium text-gray-700">{{ $clientStores->first()?->name }}</span>
                @endif

            </div>
        </header>
        @endif

        <div class="min-h-screen {{ session('store_id') ? 'pt-14' : '' }} pb-28">
            @yield('content')
        </div>

        @include('client.partials.navbar')
    </body>
@endsection
