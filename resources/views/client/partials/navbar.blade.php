@php
    $currentRoute = request()->route()->getName();
    $userName = auth()->user()->name ?? 'User';

    $navItems = [
        [
            'label' => 'Pedidos',
            'icon' => 'fa-clock-rotate-left',
            'route' => 'client.orders.index',
            'active' => false,
        ],
        [
            'label' => 'Financeiro',
            'icon' => 'fa-wallet',
            'route' => 'client.financial.index',
            'active' => false,
        ],
        [
            'label' => 'Fazer um pedido',
            'icon' => 'fa-cart-plus',
            'route' => 'client.orders.create',
            'active' => false,
            'center' => true,
        ],
        [
            'label' => 'Comércios',
            'icon' => 'fa-store',
            'route' => 'client.stores.index',
            'active' => false,
        ],
        [
            'label' => 'Perfil',
            'icon' => 'fa-user',
            'route' => 'client.profile.index',
            'active' => false,
        ],
    ];

    foreach ($navItems as $i => $item) {
        if ($item['route'] == $currentRoute) {
            $navItems[$i]['active'] = true;
        }
    }

@endphp

<nav class="fixed inset-x-0 bottom-0 z-50"
    style="padding-bottom:calc(env(safe-area-inset-bottom,0px)+1.5rem)">

    <div class="mx-auto w-full max-w-lg md:max-w-2xl lg:max-w-3xl xl:max-w-4xl">
        <div
            class="relative flex items-center justify-between bg-white/90 px-10 md:px-16 pt-3 pb-8 shadow-lg ring-1 ring-black/5 backdrop-blur rounded-t-2xl md:rounded-t-3xl">

            @foreach ($navItems as $item)

                @if (!empty($item['center']))
                    <a href="{{ route($item['route']) }}"
                        aria-label="{{ $item['label'] }}"
                        class="relative -mt-6 md:-mt-8 flex h-14 w-14 md:h-16 md:w-16 items-center justify-center rounded-2xl bg-green-600 text-white shadow-xl transition hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-white">
                        <i class="fas {{ $item['icon'] }} text-xl md:text-2xl"></i>
                    </a>
                @else
                    <a href="{{ route($item['route']) }}"
                        aria-label="{{ $item['label'] }}"
                        class="flex flex-col items-center gap-1 text-xs md:text-sm font-medium transition-colors {{ $item['active'] ? 'text-green-600' : 'text-gray-600 hover:text-gray-900' }}">

                        @if ($item['route'] === 'client.profile.index')
                            <img
                                src="https://api.dicebear.com/9.x/glass/svg?seed={{ urlencode($userName) }}"
                                alt="Profile"
                                class="h-6 w-6 md:h-8 md:w-8 rounded-full object-cover border transition-transform duration-200 hover:scale-110 {{ $item['active'] ? 'border-green-600' : 'border-transparent' }}"
                            >
                        @else
                            <i class="fas {{ $item['icon'] }} text-xl md:text-2xl"></i>
                        @endif

                        <span class="hidden sm:inline">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endif

            @endforeach

        </div>
    </div>
</nav>
