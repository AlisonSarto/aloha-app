<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Http;

class GestaoClickService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gestaoclick.url');
    }

    private function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => storage_path('certs/cacert.pem'),
            ])
            ->withHeaders([
                'Content-Type' => 'application/json',
                'access-token' => config('services.gestaoclick.token'),
                'secret-access-token' => config('services.gestaoclick.secret'),
            ]);
    }

    public function getStores(): array
    {
        $page = 1;
        $stores = [];

        do {

            $response = $this->client()
                ->get('/clientes', [
                    'pagina' => $page,
                ])
                ->throw()
                ->json();

            $stores = array_merge($stores, $response['data']);

            $page = $response['meta']['proxima_pagina'];

        } while ($page !== null);

        return $stores;
    }

    public function getStore(string $id): array
    {
        return $this->client()
            ->get("/clientes/$id")
            ->throw()
            ->json();
    }

    public function getStoreByPhone(string $phone): array
    {
        return $this->client()
            ->get('/clientes', [
                'telefone' => $phone,
            ])
            ->throw()
            ->json();
    }

    public function syncStores(): void
    {
        $page = 1;

        do {

            $response = $this->client()
                ->get('/clientes', [
                    'pagina' => $page,
                ])
                ->throw()
                ->json();

            foreach ($response['data'] as $store) {

                Store::updateOrCreate(
                    ['gestao_click_id' => $store['id']],
                    ['name' => $store['nome']]
                );

            }

            $page = $response['meta']['proxima_pagina'];

        } while ($page !== null);
    }

    public function firstOrCreateStore(array $store): array
    {
        $client = $this->client();

        $existing = $client
            ->get('/clientes', [
                'cpf_cnpj' => $store['cnpj'],
            ])
            ->throw()
            ->json();

        if (! empty($existing['data'])) {
            return $existing;
        }

        return $client
            ->post('/clientes', [
                'tipo_pessoa' => 'PJ',
                'nome' => $store['name'],
                'razao_social' => $store['legal_name'],
                'cnpj' => $store['cnpj'],
                'enderecos' => [
                    'endereco' => [
                        'cep' => $store['address_cep'],
                        'logradouro' => $store['address_street'],
                        'numero' => $store['address_number'],
                        'bairro' => $store['address_district'],
                        'nome_cidade' => $store['address_city'],
                        'estado' => $store['address_state'],
                    ],
                ],
            ])
            ->throw()
            ->json();
    }

    public function getProducts(): array
    {
        $response = $this->client()
            ->get('/produtos', ['grupo_id' => 2571246])
            ->throw()
            ->json();

        $data = $response['data'];

        $products = [];
        foreach ($data as $product) {
            $products[] = [
                'id' => $product['id'],
                'nome' => $product['nome'],
            ];
        }

        $ordem = [
            'COCO',
            'MELANCIA',
            'MARACUJÁ',
            'MORANGO',
            'PESSEGO C/ MORANGO',
            'LARANJA',
            'LIMÃO',
            'PITAYA',
            'MAÇA VERDE',
        ];

        usort($products, function ($a, $b) use ($ordem) {

            $getPos = function ($nome) use ($ordem) {
                foreach ($ordem as $index => $sabor) {
                    if (str_contains($nome, $sabor)) {
                        return $index;
                    }
                }

                return PHP_INT_MAX; // se não encontrar, vai pro final
            };

            return $getPos($a['nome']) <=> $getPos($b['nome']);
        });

        return $products;
    }

    /**
     * Lista os pedidos de uma loja no GestaoClick.
     *
     * Endpoint real esperado: GET /vendas?cliente_id={id}
     *
     * Retorno real esperado:
     * [
     *   'data' => [
     *     [
     *       'id'              => 'venda_12345',
     *       'numero'          => 'PED-2024-001',
     *       'status'          => 'aguardando',   // aguardando | confirmado | entregue | cancelado
     *       'data_emissao'    => '2024-01-15',
     *       'data_entrega'    => '2024-01-17',
     *       'valor_total'     => 140.00,
     *       'forma_pagamento' => 'pix',          // pix | boleto | dinheiro | cartao
     *       'tipo_entrega'    => 'entrega',       // entrega | retirada
     *       'itens' => [
     *         ['produto_id' => 'prod_001', 'nome' => 'Coco', 'quantidade' => 2, 'valor_unitario' => 70.00],
     *       ],
     *     ],
     *   ],
     *   'meta' => ['total' => 1, 'proxima_pagina' => null],
     * ]
     */
    public function getOrders(string $gestaoClickStoreId): array
    {
        // TODO: substituir pelo endpoint real quando disponível
        // return $this->client()
        //     ->get('/vendas', ['cliente_id' => $gestaoClickStoreId])
        //     ->throw()
        //     ->json();

        return [
            'data' => [
                [
                    'id' => 'venda_12345',
                    'numero' => 'PED-2024-001',
                    'status' => 'aguardando',
                    'data_emissao' => '2024-01-15',
                    'data_entrega' => '2024-01-17',
                    'valor_total' => 140.00,
                    'forma_pagamento' => 'pix',
                    'tipo_entrega' => 'entrega',
                    'itens' => [
                        ['produto_id' => 'prod_001', 'nome' => 'Coco',    'quantidade' => 2, 'valor_unitario' => 70.00],
                        ['produto_id' => 'prod_002', 'nome' => 'Morango', 'quantidade' => 1, 'valor_unitario' => 70.00],
                    ],
                ],
            ],
            'meta' => ['total' => 1, 'proxima_pagina' => null],
        ];
    }

    public function createOrder(string $gestaoClickStoreId, array $data): array
    {
        $store = Store::where('gestao_click_id', $gestaoClickStoreId)->firstOrFail();

        $forma_pagamento = match ($data['forma_pagamento']) {
            'cash'   => 2633094,
            'boleto' => 2219792,
            default  => 2219799, // pix
        };

        $totalQty = collect($data['itens'])->sum('quantidade');

        $priceRange = $store->priceTable->ranges()
            ->where('min_quantity', '<=', $totalQty)
            ->where(function ($q) use ($totalQty) {
                $q->whereNull('max_quantity')->orWhere('max_quantity', '>=', $totalQty);
            })
            ->orderByDesc('min_quantity')
            ->first();

        $vlr_unitario = $priceRange ? (float) $priceRange->unit_price * 28 : 0.0;

        $pedido_formatado = collect($data['itens'])->map(fn($item) => [
            'produto_id'  => $item['produto_id'],
            'quantidade'  => $item['quantidade'],
            'valor_venda' => $vlr_unitario,
        ])->sortByDesc('quantidade')->values()->toArray();

        $vlr_frete = (float) $store->shipping_amount;
        $vlr_total = ($totalQty * $vlr_unitario) + $vlr_frete;

        if ($data['tipo_entrega'] === 'retirada') {
            $observacao .= "\n>> RETIRADA NO LOCAL <<";
        } else {
            $dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
            $observacao = "=== Horários de Funcionamento ===\n";
            foreach ($store->storeHours()->orderBy('day_of_week')->get() as $hour) {
                $dia = $dias[$hour->day_of_week] ?? (string) $hour->day_of_week;

                if ($hour->is_open) {
                    $abre = \Carbon\Carbon::parse($hour->open_time)->format('H:i');
                    $fecha = \Carbon\Carbon::parse($hour->close_time)->format('H:i');

                    $observacao .= "{$dia}: {$abre} às {$fecha}\n";
                } else {
                    $observacao .= "{$dia}: Fechado\n";
                }
            }
        }

        if (!empty($data['observacao'])) {
            $observacao .= "\n\n=== Observação do Cliente ===\n" . $data['observacao'];
        }

        $data_vencimento = $data['forma_pagamento'] === 'boleto'
            ? date('Y-m-d', strtotime($data['data_entrega'] . ' +' . $store->boleto_due_days . ' days'))
            : $data['data_entrega'];

        $response = $this->client()
            ->post('/vendas', [
                'tipo'          => 'produto',
                'cliente_id'    => $gestaoClickStoreId,
                'situacao_id'   => 3395252,
                'date'          => date('Y-m-d'),
                'prazo_entrega' => $data['data_entrega'],
                'produtos'      => $pedido_formatado,
                'valor_frete'   => $vlr_frete,
                'vendedor_id'   => 1052314,
                'observacoes'   => $observacao,
                'pagamentos'    => [
                    'pagamento' => [
                        'forma_pagamento_id' => $forma_pagamento,
                        'valor'              => $vlr_total,
                        'parcelas'           => 1,
                        'data_vencimento'    => $data_vencimento,
                    ],
                ],
            ])
            ->throw()
            ->json();

        return [
            'data' => [
                'id'           => $response['data']['id']         ?? '',
                'numero'       => $response['data']['codigo']      ?? '',
                'status'       => $response['data']['nome_situacao']      ?? 'aguardando',
                'data_entrega' => $data['data_entrega'],
                'valor_total'  => $response['data']['valor_total'] ?? $vlr_total,
            ],
        ];
    }
}
