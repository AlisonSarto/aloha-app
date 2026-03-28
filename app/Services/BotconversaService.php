<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BotconversaService
{
    public function newOrderNotification(
        string $phone,
        string $saleId,
        string $saleCode,
        string $storeName,
    ): array
    {
        $webhookUrl = config('services.botconversa.webhooks.new_order');

        if (empty($webhookUrl)) {
            return [
                'success' => false,
                'message' => 'Botconversa webhook not configured.',
            ];
        }

        $response = Http::withOptions([
                'verify' => storage_path('certs/cacert.pem'),
            ])
            ->asJson()
            ->post($webhookUrl, [
                'telefone' => '+' . $phone,
                'id_venda' => $saleId,
                'codigo_venda' => $saleCode,
                'nome_comercio' => $storeName,
            ])
            ->throw();

        return [
            'success' => true,
            'status' => $response->status(),
            'send' => [
                'telefone' => $phone,
                'id_venda' => $saleId,
                'codigo_venda' => $saleCode,
                'nome_comercio' => $storeName,
            ],
            'data' => $response->json(),
        ];
    }

}
