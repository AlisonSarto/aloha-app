@extends('layouts.client')

@section('title', 'Perfil')

@section('body')
Ver perfil
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-danger">Sair</button>
</form>
@section('body')
