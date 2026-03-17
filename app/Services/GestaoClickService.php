<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Store;

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
                    'pagina' => $page
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
            ->get("/clientes", [
                'telefone' => $phone
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
                    'pagina' => $page
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
                'cpf_cnpj' => $store['cnpj']
            ])
            ->throw()
            ->json();

        if (!empty($existing['data'])) {
            return $existing;
        }

        return $client
            ->post('/clientes', [
                'tipo_pessoa' => 'PJ',
                'nome' => $store['fantasy_name'],
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
                    ]
                ]
            ])
            ->throw()
            ->json();
    }
}
