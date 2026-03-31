@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    {{-- Welcome banner --}}
    <div class="rounded-2xl bg-gradient-to-r from-green-600 to-green-700 p-6 shadow-lg mb-8 mt-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-green-100 text-xs font-semibold uppercase tracking-widest mb-1">Painel Administrativo</p>
                <p class="text-white text-xl font-bold">Olá, {{ auth()->user()->name }}!</p>
                <p class="text-green-200 text-sm mt-1">Use os atalhos abaixo ou o menu para gerenciar o sistema.</p>
            </div>
            <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-white/20 items-center justify-center flex-shrink-0">
                <i class="fas fa-leaf text-white text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Quick access cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">

        <a href="{{ route('admin.clients.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-users text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Clientes</p>
                <p class="text-xs text-gray-400 mt-0.5">Gerenciar e vincular comércios</p>
            </div>
        </a>

        <a href="{{ route('admin.stores.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-store text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Comércios</p>
                <p class="text-xs text-gray-400 mt-0.5">Configurar lojas e horários</p>
            </div>
        </a>

        <a href="{{ route('admin.sellers.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-handshake text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Vendedores</p>
                <p class="text-xs text-gray-400 mt-0.5">Comissões e metas</p>
            </div>
        </a>

        <a href="{{ route('admin.price-tables.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-table text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Tabela de Preços</p>
                <p class="text-xs text-gray-400 mt-0.5">Faixas e valores</p>
            </div>
        </a>

        <a href="{{ route('admin.coupons.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-ticket text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Cupons</p>
                <p class="text-xs text-gray-400 mt-0.5">Descontos e promoções</p>
            </div>
        </a>

        <a href="{{ route('admin.delivery-config.edit') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-truck text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Entrega</p>
                <p class="text-xs text-gray-400 mt-0.5">Dias e prazo de entrega</p>
            </div>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="group rounded-xl bg-white shadow-sm ring-1 ring-black/5 p-5 flex flex-col gap-3 hover:ring-green-300 hover:shadow-md transition-all">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center group-hover:bg-green-200 transition">
                <i class="fas fa-user-shield text-green-700"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900">Usuários</p>
                <p class="text-xs text-gray-400 mt-0.5">Acesso ao sistema</p>
            </div>
        </a>

    </div>

@endsection
