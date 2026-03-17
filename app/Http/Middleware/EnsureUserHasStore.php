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

        if (!$user->client || $user->client->stores()->doesntExist()) {
            return redirect()->route('client.stores.register');
        }

        return $next($request);
    }
}
