<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenCNPJ
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.open_cnpj.url');
    }

    private function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withOptions([
                'verify' => storage_path('certs/cacert.pem'),
            ])
            ->withHeaders([
                'Content-Type' => 'application/json',
            ]);
    }

    public function getCNPJ($cnpj): array
    {
        $response = $this->client()
            ->get("/$cnpj");

        return [
            'status' => $response->status(),
            'data' => $response->json() ?? [],
        ];
    }
}
