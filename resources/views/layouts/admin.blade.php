<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Aloha App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        form {
            margin: 0;
        }
    </style>
</head>
<body>
    <nav class="relative bg-gray-800">
        <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                    <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:-outline-offset-1 focus:outline-indigo-500">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                    <div class="flex shrink-0 items-center">
                        <img src="{{ asset('favicon.ico') }}" alt="Your Company" class="h-8 w-auto"/>
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4" id="desktop-menu">
                        </div>
                    </div>
                </div>

                <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                    <div class="relative ml-3">
                        <button id="user-menu-button" class="relative flex rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500" aria-expanded="false" aria-haspopup="true">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            <img src="https://api.dicebear.com/9.x/glass/svg?seed={{ auth()->user()->name }}" alt="" class="size-8 rounded-full bg-gray-800 outline -outline-offset-1 outline-white/10"/>
                        </button>

                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button">
                            <div class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                Logado como: {{ auth()->user()->name }}
                            </div>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0px">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <el-disclosure id="mobile-menu" hidden class="block sm:hidden">
            <div class="space-y-1 px-2 pt-2 pb-3" id="mobile-menu-items">
            </div>
        </el-disclosure>
    </nav>

    <main class="container mx-auto p-4">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('button[command="--toggle"]');
            const mobileMenu = document.getElementById('mobile-menu');

            if (toggleBtn && mobileMenu) {
                mobileMenu.classList.add('hidden');
                toggleBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            const userBtn = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            if (userBtn && userMenu) {
                userBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function() {
                    if (!userMenu.classList.contains('hidden')) {
                        userMenu.classList.add('hidden');
                    }
                });
            }

            // Menu data
            const menuData = {
                "menu": [
                    {
                        "name": "Home",
                        "url": "/admin/home",
                    },
                    {
                        "name": "Clientes",
                        "url": "/admin/clients",
                    },
                    {
                        "name": "Comércios",
                        "url": "/admin/stores",
                    },
                    {
                        "name": "Vendedores",
                        "url": "/admin/sellers",
                    },
                    {
                        "name": "Tabela de Preços",
                        "url": "/admin/price-tables",
                    },
                    {
                        "name": "Usuários",
                        "url": "/admin/users",
                    }
                ]
            };

            // Load menu
            const desktopMenuLink = document.getElementById('desktop-menu');
            const mobileMenuLink = document.getElementById('mobile-menu-items');
            menuData.menu.forEach(item => {

                // PC
                var a = document.createElement('a');
                a.href = item.url;
                a.className = 'rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white';
                a.textContent = item.name;
                desktopMenuLink.appendChild(a);

                // Mobile
                a = document.createElement('a');
                a.href = item.url;
                a.className = 'block rounded-md px-3 py-2 text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white';
                a.textContent = item.name;
                mobileMenuLink.appendChild(a);

            });
        });
    </script>
</body>
</html>
