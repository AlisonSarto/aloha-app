<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

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

        $payload = [
            'telefone' => '+' . $phone,
            'id_venda' => $saleId,
            'codigo_venda' => $saleCode,
            'nome_comercio' => $storeName,
        ];

        try {
            $response = Http::withOptions([
                    'verify' => storage_path('certs/cacert.pem'),
                ])
                ->asJson()
                ->post($webhookUrl, $payload)
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
        } catch (RequestException $e) {
            return [
                'success' => false,
                'status' => $e->response?->status(),
                'message' => 'Botconversa request failed.',
                'send' => [
                    'telefone' => $phone,
                    'id_venda' => $saleId,
                    'codigo_venda' => $saleCode,
                    'nome_comercio' => $storeName,
                ],
                'data' => $e->response?->json() ?? $e->response?->body(),
                'error' => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Unexpected Botconversa error.',
                'send' => [
                    'telefone' => $phone,
                    'id_venda' => $saleId,
                    'codigo_venda' => $saleCode,
                    'nome_comercio' => $storeName,
                ],
                'error' => $e->getMessage(),
            ];
        }
    }

}
