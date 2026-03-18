<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OpenCNPJ;
use App\Services\GestaoClickService;

class StoreController extends Controller
{
    public function index()
    {
        return view('client.stores.index');
    }

    public function setActive(Request $request)
    {
        $request->validate([
            'store_id' => 'required|integer'
        ]);

        $user = auth()->user();

        if (!$user->client->stores()->where('stores.id', $request->store_id)->exists()) {
            abort(403);
        }

        session(['store_id' => $request->store_id]);

        return redirect()->back();
    }

    public function registerForm()
    {
        return view('client.stores.register');
    }

    public function verifyCNPJ(Request $request, OpenCNPJ $openCnpj): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|regex:/^\d{14}$/'
        ], [
            'cnpj.regex' => 'O CNPJ deve conter 14 dígitos.'
        ]);

        $cnpj = str_replace(['.', '-'], '', $request->input('cnpj'));

        // Primeiro, verificar se a store já existe no banco de dados
        $existingStore = Store::where('cnpj', $cnpj)->first();

        if ($existingStore) {
            return response()->json([
                'success' => true,
                'store' => [
                    'cnpj' => $existingStore->cnpj,
                    'legal_name' => $existingStore->legal_name,
                    'fantasy_name' => $existingStore->fantasy_name,
                ],
                'exists_in_database' => true,
                'message' => 'Encontramos esse CNPJ no nosso sistema! Confirme se os dados estão corretos.'
            ]);
        }

        // Se não existe no banco, buscar na API externa
        try {
            $response = $openCnpj->getCNPJ($cnpj);

            if ($response['status'] === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ não encontrado. Verifique o número e tente novamente.'
                ], 404);
            }

            if ($response['status'] === 429) {
                //espera 1s e tenta novamente
                sleep(1);
                $response = $openCnpj->getCNPJ($cnpj);

                if ($response['status'] === 404) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CNPJ não encontrado. Verifique o número e tente novamente.'
                    ], 404);
                }

                if ($response['status'] === 429) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Limite de requisições atingido. Por favor, tente novamente mais tarde.'
                    ], 429);
                }
            }

            $data = $response['data'] ?? [];

            $storeData = [
                'cnpj' => $cnpj,
                'legal_name' => $data['razao_social'] ?? '',
                'fantasy_name' => $data['nome_fantasia'] ?? '',
            ];

            return response()->json([
                'success' => true,
                'store' => $storeData,
                'exists_in_database' => false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Loja não encontrada. Verifique o CNPJ e tente novamente.'
            ], 404);
        }
    }

    public function confirmStep1(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|regex:/^\d{14}$/',
            'legal_name' => 'required|string|max:255',
            'fantasy_name' => 'required|string|max:255',
            'exists_in_database' => 'sometimes|boolean'
        ]);

        $cnpj = str_replace(['.', '-'], '', $request->input('cnpj'));
        $existsInDatabase = $request->input('exists_in_database', false);

        try {
            // Se já existe no banco, verificar se os dados enviados coincidem com os do banco
            if ($existsInDatabase) {
                $existingStore = Store::where('cnpj', $cnpj)->first();

                if (!$existingStore) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Loja não encontrada no sistema.'
                    ], 404);
                }

                // Verificar se os dados enviados coincidem exatamente com os do banco
                if ($existingStore->legal_name !== $request->input('legal_name') ||
                    $existingStore->fantasy_name !== $request->input('fantasy_name')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Os dados não correspondem aos cadastrados no sistema. Entre em contato com o suporte.'
                    ], 400);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Dados confirmados! Esta loja já está cadastrada no sistema.',
                    'data' => [
                        'cnpj' => $existingStore->cnpj,
                        'legal_name' => $existingStore->legal_name,
                        'fantasy_name' => $existingStore->fantasy_name,
                        'exists_in_database' => true
                    ]
                ]);
            }

            // Para lojas novas (não existem no banco)
            return response()->json([
                'success' => true,
                'message' => 'Dados básicos confirmados!',
                'data' => [
                    'cnpj' => $request->input('cnpj'),
                    'legal_name' => $request->input('legal_name'),
                    'fantasy_name' => $request->input('fantasy_name'),
                    'exists_in_database' => false
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmStep2(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj' => 'required|regex:/^\d{14}$/',
            'address_cep' => 'required|regex:/^\d{8}$/',
            'address_street' => 'required|string|max:255',
            'address_number' => 'required|string|max:20',
            'address_district' => 'required|string|max:100',
            'address_city' => 'required|string|max:100',
            'address_state' => 'required|regex:/^[A-Z]{2}$/'
        ], [
            'address_cep.regex' => 'CEP deve conter 8 dígitos.',
            'address_state.regex' => 'Estado deve ser uma sigla válida (ex: SP).'
        ]);

        try {
            return response()->json([
                'success' => true,
                'message' => 'Endereço confirmado!',
                'data' => [
                    'address_cep' => $request->input('address_cep'),
                    'address_street' => $request->input('address_street'),
                    'address_number' => $request->input('address_number'),
                    'address_complement' => $request->input('address_complement', ''),
                    'address_district' => $request->input('address_district'),
                    'address_city' => $request->input('address_city'),
                    'address_state' => $request->input('address_state')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar endereço: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmStep3(Request $request, GestaoClickService $gestaoClick): JsonResponse
    {
        $existsInDatabase = $request->input('exists_in_database', false);

        // Validações mais flexíveis para stores existentes
        $rules = [
            'cnpj' => 'required|regex:/^\d{14}$/',
            'legal_name' => 'required|string|max:255',
            'fantasy_name' => 'required|string|max:255',
            'exists_in_database' => 'sometimes|boolean'
        ];

        if (!$existsInDatabase) {
            // Para stores novas, todos os campos são obrigatórios
            $rules = array_merge($rules, [
                'address_cep' => 'required|regex:/^\d{8}$/',
                'address_street' => 'required|string|max:255',
                'address_number' => 'required|string|max:20',
                'address_district' => 'required|string|max:100',
                'address_city' => 'required|string|max:100',
                'address_state' => 'required|regex:/^[A-Z]{2}$/',
                'hours' => 'required|array'
            ]);
        } else {
            // Para stores existentes, campos de endereço são opcionais/dummy
            $rules = array_merge($rules, [
                'address_cep' => 'sometimes|regex:/^\d{8}$/',
                'address_street' => 'sometimes|string|max:255',
                'address_number' => 'sometimes|string|max:20',
                'address_district' => 'sometimes|string|max:100',
                'address_city' => 'sometimes|string|max:100',
                'address_state' => 'sometimes|regex:/^[A-Z]{2}$/',
                'hours' => 'sometimes|array'
            ]);
        }

        $validated = $request->validate($rules, [
            'address_cep.regex' => 'CEP deve conter 8 dígitos.',
            'address_state.regex' => 'Estado deve ser uma sigla válida (ex: SP).'
        ]);

        $client = auth()->user()->client;

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui perfil de cliente.'
            ], 400);
        }

        try {
            $cnpj = str_replace(['.', '-', '/'], '', $request->input('cnpj'));
            $existsInDatabase = $request->input('exists_in_database', false);

            $store = DB::transaction(function () use ($cnpj, $validated, $client, $gestaoClick, $existsInDatabase) {
                $store = Store::where('cnpj', $cnpj)->first();

                if ($existsInDatabase && $store) {
                    // Store já existe no banco - apenas vincular ao cliente
                    $isAlreadyLinked = $client->stores()->where('stores.id', $store->id)->exists();

                    if ($isAlreadyLinked) {
                        throw new \Exception('Esta loja já está vinculada à sua conta.');
                    }

                    // Vincular a loja existente ao cliente
                    $client->stores()->attach($store->id);

                    return $store;
                } else {
                    // Store nova - criar no Gestão Click e no banco
                    $gcStore = $gestaoClick->firstOrCreateStore($validated);
                    $gestaoClickId = $gcStore['data'][0]['id'] ?? $gcStore['data']['id'];

                    $store = Store::firstOrNew(['cnpj' => $cnpj]);

                    $store->gestao_click_id = $gestaoClickId;

                    $store->fill([
                        'name' => $validated['fantasy_name'],
                        'legal_name' => $validated['legal_name'],
                        'fantasy_name' => $validated['fantasy_name'],
                        'address_cep' => $validated['address_cep'],
                        'address_street' => $validated['address_street'],
                        'address_number' => $validated['address_number'],
                        'address_complement' => $validated['address_complement'] ?? '',
                        'address_district' => $validated['address_district'],
                        'address_city' => $validated['address_city'],
                        'address_state' => $validated['address_state'],
                    ]);

                    $store->save();

                    // Sincronizar horários (deletar antigos e criar novos)
                    $store->storeHours()->delete();

                    $hoursData = [];
                    foreach ($validated['hours'] as $dayOfWeek => $hourData) {
                        $hoursData[] = [
                            'day_of_week' => $dayOfWeek,
                            'open_time' => $hourData['is_open'] ? $hourData['open_time'] : null,
                            'close_time' => $hourData['is_open'] ? $hourData['close_time'] : null,
                            'is_open' => $hourData['is_open'] ?? true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (!empty($hoursData)) {
                        $store->storeHours()->createMany($hoursData);
                    }

                    // Vincular a loja ao cliente
                    $client->stores()->syncWithoutDetaching([$store->id]);

                    return $store;
                }
            });

            $message = $existsInDatabase && Store::where('cnpj', $cnpj)->exists()
                ? 'Loja vinculada com sucesso! Esta loja já estava cadastrada no sistema.'
                : 'Loja vinculada com sucesso!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'store' => $store
            ]);

        } catch (\Exception $e) {
            // Se a mensagem for sobre loja já vinculada, retornar como erro controlado
            if (strpos($e->getMessage(), 'já está vinculada') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            \Illuminate\Support\Facades\Log::error('Erro ao vincular loja', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $validated ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao vincular loja.'
            ], 500);
        }
    }

    private function validateCNPJ(string $cnpj): bool
    {
        return preg_match('/^\d{14}$/', $cnpj) === 1;
    }

    private function validateAddressData(array $data): bool
    {
        return preg_match('/^\d{8}$/', $data['address_cep'] ?? '') === 1 &&
               preg_match('/^[A-Z]{2}$/', $data['address_state'] ?? '') === 1 &&
               !empty($data['address_street']) &&
               !empty($data['address_number']) &&
               !empty($data['address_district']) &&
               !empty($data['address_city']);
    }

}
