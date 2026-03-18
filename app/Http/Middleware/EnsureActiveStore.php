<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!session()->has('store_id')) {
            return redirect()->route('client.stores.index');
        }

        if (!$user->client->stores()->where('stores.id', session('store_id'))->exists()) {
            session()->forget('store_id');
            return redirect()->route('client.stores.index');
        }

        return $next($request);
    }
}
