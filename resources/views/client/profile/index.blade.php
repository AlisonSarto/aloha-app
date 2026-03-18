@extends('layouts.client')

@section('title', 'Perfil')

@section('body')

    <h1 class="mb-6 text-2xl font-bold text-gray-900">Meu perfil</h1>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="inline-flex h-12 items-center justify-center rounded-md bg-red-500 px-6 font-medium text-neutral-50 shadow-lg shadow-neutral-500/20 transition active:scale-95">
            Sair
        </button>
    </form>

@section('body')
