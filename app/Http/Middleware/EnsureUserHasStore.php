<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user->client) {
            return redirect()->route('client.stores.register');
        }

        $stores = $user->client->stores;

        if ($stores->isEmpty()) {
            return redirect()->route('client.stores.register');
        }

        if ($stores->count() === 1 && !session()->has('store_id')) {
            session(['store_id' => $stores->first()->id]);
        }

        return $next($request);
    }
}
