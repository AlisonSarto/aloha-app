@extends('layouts.app')

@section('head')
    <title>@yield('title', 'Vendedor') - Aloha App</title>
@endsection

@section('body')
    <body class="min-h-screen bg-gradient-to-b from-green-50 to-white text-gray-900">

        {{-- ── HEADER ──────────────────────────────────────────────────────────── --}}
        <header class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur shadow-sm ring-1 ring-black/5">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-3">

                {{-- Brand + hamburger --}}
                <div class="flex items-center gap-2">
                    <button id="mob-menu-btn"
                        class="sm:hidden inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                    <a href="{{ route('seller.home') }}" class="flex items-center gap-2 font-bold text-gray-800">
                        <img src="{{ asset('favicon.ico') }}" alt="Aloha" class="w-8 h-8">
                        <span class="hidden sm:inline text-lg">Aloha App</span>
                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Vendedor</span>
                    </a>
                </div>

                {{-- Desktop navigation --}}
                <nav class="hidden sm:flex items-center gap-0.5" id="desktop-menu"></nav>

                {{-- User menu --}}
                <div class="relative">
                    <button id="user-menu-btn"
                        class="flex items-center gap-2 rounded-xl px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                        <img src="https://api.dicebear.com/9.x/glass/svg?seed={{ auth()->user()->name }}"
                            alt="" class="w-8 h-8 rounded-full ring-2 ring-green-200"/>
                        <span class="hidden md:inline max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>

                    <div id="user-menu"
                        class="hidden absolute right-0 mt-2 w-52 rounded-xl bg-white py-1.5 shadow-lg ring-1 ring-black/10 z-50">
                        <div class="px-4 py-2.5 text-xs text-gray-500 border-b border-gray-100 truncate">
                            {{ auth()->user()->email }}
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-right-from-bracket text-xs"></i> Sair
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            {{-- Mobile nav dropdown --}}
            <div id="mobile-menu"
                class="hidden sm:hidden border-t border-gray-100 bg-white px-4 py-2 space-y-0.5"></div>
        </header>

        {{-- ── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
        <main class="mx-auto max-w-7xl px-4 pt-20 pb-12">
            @if(session('success'))
                <div class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-600"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const userBtn  = document.getElementById('user-menu-btn');
                const userMenu = document.getElementById('user-menu');
                userBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', function () {
                    userMenu.classList.add('hidden');
                });

                const mobBtn  = document.getElementById('mob-menu-btn');
                const mobMenu = document.getElementById('mobile-menu');
                mobBtn.addEventListener('click', function () {
                    mobMenu.classList.toggle('hidden');
                });

                const menuData = [
                    { name: 'Dashboard',    url: '/seller/home',              icon: 'fa-gauge' },
                    { name: 'Minhas Lojas', url: '/seller/stores',            icon: 'fa-store' },
                    { name: 'Comissões',    url: '/seller/reports/commissions', icon: 'fa-coins' },
                    { name: 'Lojas',        url: '/seller/reports/stores',     icon: 'fa-chart-bar' },
                    { name: 'Metas',        url: '/seller/reports/goals',      icon: 'fa-bullseye' },
                ];

                const currentPath = window.location.pathname;
                const desktop     = document.getElementById('desktop-menu');
                const mobile      = document.getElementById('mobile-menu');

                menuData.forEach(function (item) {
                    const isActive = currentPath.startsWith(item.url);

                    const a = document.createElement('a');
                    a.href = item.url;
                    a.className = isActive
                        ? 'flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-green-700 bg-green-100'
                        : 'flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition';
                    a.innerHTML = '<i class="fas ' + item.icon + ' text-xs"></i> ' + item.name;
                    desktop.appendChild(a);

                    const ma = document.createElement('a');
                    ma.href = item.url;
                    ma.className = isActive
                        ? 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-green-700 bg-green-50'
                        : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition';
                    ma.innerHTML = '<i class="fas ' + item.icon + ' text-xs w-4 text-center ' + (isActive ? 'text-green-600' : 'text-gray-400') + '"></i>' + item.name;
                    mobile.appendChild(ma);
                });
            });
        </script>
    </body>
@endsection
