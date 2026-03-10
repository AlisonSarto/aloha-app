<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GestaoClickService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.gestaoclick.url');
    }

    private function client()
    {
        return Http::withOptions([
            'verify' => storage_path('certs/cacert.pem')
        ])->withHeaders([
            'Content-Type' => 'application/json',
            'access-token' => config('services.gestaoclick.token'),
            'secret-access-token' => config('services.gestaoclick.secret'),
        ]);
    }

    public function searchStores($search)
    {
        $response = $this->client()->get(
            config('services.gestaoclick.url').'/stores',
            ['search' => $search]
        );

        return $response->json();
    }
}
