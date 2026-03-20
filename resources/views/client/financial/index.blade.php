@extends('layouts.client')

@section('title', 'Financeiro')

@section('body')
    <h1 class="mb-4 text-2xl font-bold text-gray-900">Financeiro</h1>

    <div class="flex flex-col items-center justify-center py-20 text-center">

        <!-- Icon -->
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center mb-6 shadow-md">
            <i class="fas fa-chart-line text-white text-3xl"></i>
        </div>

        <!-- Title -->
        <h2 class="text-xl font-semibold text-gray-800">
            Em breve 🚀
        </h2>

        <!-- Description -->
        <p class="text-gray-500 mt-2 max-w-xs">
            Estamos preparando um módulo financeiro completo para você.
        </p>

        <!-- Extra hype -->
        <p class="text-sm text-gray-400 mt-3">
            Gerencie os seus boletos e muito mais!
        </p>

        <!-- CTA opcional -->
        <a href="{{ route('client.orders.create') }}"
            class="mt-8 inline-flex items-center gap-2 rounded-xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-green-700 transition">
            <i class="fas fa-cart-plus"></i> Fazer um pedido
        </a>

    </div>
@section('body')
