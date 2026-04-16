<?php

namespace App\Http\Middleware;

use App\Services\GestaoClickService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureNoOverdueInvoices
{
    public function __construct(private GestaoClickService $gc) {}

    public function handle(Request $request, Closure $next): Response
    {
        $store = activeStore();

        if (! $store) {
            return $next($request);
        }

        $cacheKey  = "store_overdue_boletos_{$store->id}";
        $hasOverdue = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($store) {
            try {
                $atrasados = $this->gc->getRecebimentos($store->gestao_click_id, 'at');
                return count($atrasados) > 0;
            } catch (\Throwable) {
                // If the API is unavailable, don't block the user
                return false;
            }
        });

        if ($hasOverdue) {
            return redirect()->route('client.financial.index')
                ->with('overdue_block', 'Você possui boleto(s) em atraso. Por favor, regularize sua situação antes de fazer um novo pedido.');
        }

        return $next($request);
    }
}
