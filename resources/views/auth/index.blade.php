@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">

  <div class="mx-auto w-full sm:mx-auto sm:w-full sm:max-w-sm">
    <img src="{{ asset('favicon.ico') }}" alt="Aloha App logo" class="mx-auto h-12 w-auto" />
    <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Bem-vindo ao Aloha App</h2>
    <p class="mt-2 text-center text-sm text-gray-600">Acesse sua conta para continuar</p>
  </div>

  <div class="mx-auto w-full sm:mx-auto sm:w-full sm:max-w-md">
    <form action="{{ route('login') }}" method="POST" class="space-y-6 bg-white px-6 rounded-lg shadow-sm">
      @csrf

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <div class="mt-1">
          <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" aria-describedby="email-error" />
        </div>
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
        <div class="mt-1">
          <input id="password" name="password" type="password" required autocomplete="current-password" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-indigo-500" aria-describedby="password-error" />
          @error('error')
            <p id="password-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div>
        <button type="submit" class="my-2 w-full flex justify-center rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">Entrar</button>
      </div>

    </form>

  </div>
</div>
@endsection
