@extends('layouts.app')

@section('head')
    <title>@yield('title') - Aloha App</title>
@endsection

@section('body')
    <body class="min-h-screen bg-gradient-to-b from-green-50 to-white text-gray-900">

        {{-- ── SIDEBAR OVERLAY (mobile) ───────────────────────────────────────── --}}
        <div id="sidebar-overlay"
            class="fixed inset-0 z-40 bg-black/40 hidden lg:hidden"></div>

        {{-- ── SIDEBAR ─────────────────────────────────────────────────────────── --}}
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg flex flex-col
                   -translate-x-full lg:translate-x-0 transition-transform duration-300">

            {{-- Brand --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100 shrink-0">
                <img src="{{ asset('favicon.ico') }}" alt="Aloha" class="w-9 h-9 shrink-0">
                <div class="min-w-0">
                    <p class="font-bold text-gray-800 text-base leading-tight">Aloha App</p>
                    <span class="inline-block text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full mt-0.5">
                        Admin
                    </span>
                </div>
            </div>

            {{-- Nav groups (injected by JS) --}}
            <nav id="sidebar-nav" class="flex-1 overflow-y-auto px-3 py-3"></nav>

            {{-- User section --}}
            <div class="border-t border-gray-100 p-3 shrink-0">
                <div class="flex items-center gap-2.5 px-2 py-2 mb-1">
                    <img src="https://api.dicebear.com/9.x/glass/svg?seed={{ auth()->user()->name }}"
                        alt="" class="w-8 h-8 rounded-full ring-2 ring-green-200 shrink-0"/>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition">
                        <i class="fas fa-right-from-bracket text-xs"></i> Sair
                    </button>
                </form>
            </div>
        </aside>

        {{-- ── TOP BAR (mobile only) ───────────────────────────────────────────── --}}
        <header class="lg:hidden fixed inset-x-0 top-0 z-30 bg-white/90 backdrop-blur shadow-sm ring-1 ring-black/5">
            <div class="flex items-center gap-2 px-4 py-3">
                <button id="mob-menu-btn"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <a href="/admin/home" class="flex items-center gap-2 font-bold text-gray-800">
                    <img src="{{ asset('favicon.ico') }}" alt="Aloha" class="w-7 h-7">
                    <span class="text-base">Aloha App</span>
                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Admin</span>
                </a>
            </div>
        </header>

        {{-- ── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
        <main class="lg:ml-64 min-h-screen px-2 pt-20 lg:pt-8 pb-12">
            <div class="max-w-6xl mx-auto">
                @yield('content')
            </div>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ── Sidebar toggle (mobile) ──
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                const mobBtn  = document.getElementById('mob-menu-btn');

                function openSidebar() {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                }
                function closeSidebar() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                }

                mobBtn.addEventListener('click', function () {
                    sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
                });
                overlay.addEventListener('click', closeSidebar);

                // ── Menu groups ──
                const menuGroups = [
                    {
                        group: 'Geral',
                        items: [
                            { name: 'Dashboard', url: '/admin/home', icon: 'fa-house' },
                        ]
                    },
                    {
                        group: 'ERP',
                        items: [
                            { name: 'Unidades', url: '/admin/tenants', icon: 'Unidades', icon: 'fa-building' }
                        ]
                    },
                    {
                        group: 'Pessoas',
                        items: [
                            { name: 'Clientes',   url: '/admin/clients',  icon: 'fa-users' },
                            { name: 'Vendedores', url: '/admin/sellers',  icon: 'fa-handshake' },
                            { name: 'Usuários',   url: '/admin/users',    icon: 'fa-user-shield' },
                        ]
                    },
                    {
                        group: 'Vendas',
                        items: [
                            { name: 'Comissões',  url: '/admin/commissions',    icon: 'fa-coins' },
                            { name: 'Aprovações', url: '/admin/sellers/claims', icon: 'fa-check-circle' },
                        ]
                    },
                    {
                        group: 'Configurações',
                        items: [
                            { name: 'Comércios',        url: '/admin/stores',          icon: 'fa-store' },
                            { name: 'Tabela de Preços', url: '/admin/price-tables',    icon: 'fa-table' },
                            { name: 'Cupons',           url: '/admin/coupons',         icon: 'fa-ticket' },
                            { name: 'Entrega',          url: '/admin/delivery-config', icon: 'fa-truck' },
                        ]
                    },
                ];

                const currentPath = window.location.pathname;
                const nav         = document.getElementById('sidebar-nav');
                const STORAGE_KEY = 'admin_sidebar_collapsed';

                let collapsed = {};
                try { collapsed = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); } catch (e) {}

                menuGroups.forEach(function (group) {
                    const groupId   = 'group-' + group.group.toLowerCase().replace(/\s+/g, '-');
                    const hasActive = group.items.some(function (i) { return currentPath.startsWith(i.url); });
                    const isCollapsed = hasActive ? false : (collapsed[groupId] === true);

                    const wrapper = document.createElement('div');
                    wrapper.className = 'mb-1';

                    // Group header
                    const header = document.createElement('button');
                    header.type = 'button';
                    header.className = 'w-full flex items-center justify-between px-2 py-1.5 mb-0.5 rounded-md text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition select-none';
                    header.innerHTML = '<span>' + group.group + '</span>'
                        + '<i class="fas fa-chevron-down text-[10px] transition-transform duration-200'
                        + (isCollapsed ? ' -rotate-90' : '') + '"></i>';

                    // Items container
                    const itemsEl = document.createElement('div');
                    itemsEl.className = 'space-y-0.5 overflow-hidden transition-all duration-200';
                    itemsEl.style.maxHeight = isCollapsed ? '0px' : '500px';

                    group.items.forEach(function (item) {
                        const isActive = currentPath.startsWith(item.url);
                        const a = document.createElement('a');
                        a.href = item.url;
                        a.className = isActive
                            ? 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-semibold text-green-700 bg-green-50'
                            : 'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition';
                        a.innerHTML = '<i class="fas ' + item.icon + ' w-4 text-center text-sm shrink-0 '
                            + (isActive ? 'text-green-600' : 'text-gray-400') + '"></i>'
                            + '<span class="truncate">' + item.name + '</span>';
                        itemsEl.appendChild(a);
                    });

                    // Toggle collapse
                    header.addEventListener('click', function () {
                        const chevron      = header.querySelector('i');
                        const nowCollapsed = itemsEl.style.maxHeight !== '0px';
                        itemsEl.style.maxHeight = nowCollapsed ? '0px' : '500px';
                        chevron.classList.toggle('-rotate-90', nowCollapsed);
                        if (!hasActive) {
                            collapsed[groupId] = nowCollapsed;
                            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(collapsed)); } catch (e) {}
                        }
                    });

                    wrapper.appendChild(header);
                    wrapper.appendChild(itemsEl);
                    nav.appendChild(wrapper);
                });
            });
        </script>
    </body>
@endsection
