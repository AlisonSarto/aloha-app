<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\GestaoClickService;

class FinancialController extends Controller
{
    public function __construct(private GestaoClickService $gc) {}

    public function index()
    {
        $store = activeStore();

        $atrasados = $this->buildBoletos(
            $this->gc->getRecebimentos($store->gestao_click_id, 'at')
        );

        $atrasadosCodigos = array_column($atrasados, 'codigo');

        $emAberto = array_filter(
            $this->buildBoletos($this->gc->getRecebimentos($store->gestao_click_id, 'ab')),
            fn($b) => ! in_array($b['codigo'], $atrasadosCodigos)
        );

        return view('client.financial.index', compact('atrasados', 'emAberto', 'store'));
    }

    /**
     * Enriches raw recebimentos with individual boleto links from the public cobrança API.
     */
    private function buildBoletos(array $recebimentos): array
    {
        $boletos = [];

        foreach ($recebimentos as $rec) {
            $parts  = explode(' ', $rec['descricao'] ?? '');
            $codigo = $parts[3] ?? null;

            $boletoLinks = [];
            if ($codigo) {
                $venda = $this->gc->getOrderByCode($codigo);
                if ($venda && isset($venda['hash'])) {
                    try {
                        $boletoLinks = $this->gc->getBoletosFromCobranca($venda['hash']);
                    } catch (\Throwable $e) {
                        \Log::error('getBoletosFromCobranca failed', [
                            'codigo'  => $codigo,
                            'hash'    => $venda['hash'],
                            'error'   => $e->getMessage(),
                        ]);
                    }
                } else {
                    \Log::warning('getOrderByCode returned no hash', [
                        'codigo' => $codigo,
                        'venda'  => $venda,
                    ]);
                }
            }

            $boletos[] = [
                'codigo'          => $codigo,
                'data_vencimento' => $rec['data_vencimento'] ?? null,
                'valor'           => $rec['valor_total'] ?? 0,
                'descricao'       => $rec['descricao'] ?? '',
                'boleto_links'    => $boletoLinks,
            ];
        }

        return $boletos;
    }
}
