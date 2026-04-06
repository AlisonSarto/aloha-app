<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\SellerStoreClaim;
use App\Services\GestaoClickService;
use App\Services\OpenCNPJ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $seller = auth()->user()->seller;
        $stores = Store::where('seller_id', $seller->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('seller.stores.index', compact('stores'));
    }

    public function registerForm()
    {
        return view('seller.stores.register');
    }

    /** Proxy to the same CNPJ verification logic — but adds seller-specific handling */
    public function verifyCNPJ(Request $request, OpenCNPJ $openCnpj): JsonResponse
    {
        $request->validate(['cnpj' => 'required|regex:/^\d{14}$/']);
        $cnpj = preg_replace('/\D/', '', $request->cnpj);

        $existing = Store::where('cnpj', $cnpj)->first();

        if ($existing) {
            $hasSeller = $existing->seller_id !== null;

            return response()->json([
                'success'             => true,
                'exists_in_database'  => true,
                'already_has_seller'  => $hasSeller,
                'store'               => [
                    'cnpj'       => $existing->cnpj,
                    'legal_name' => $existing->legal_name,
                    'name'       => $existing->name,
                ],
                'message' => $hasSeller
                    ? 'Esta loja já possui vendedor vinculado. Uma solicitação será enviada para aprovação do admin.'
                    : 'Encontramos esse CNPJ no nosso sistema! Confirme os dados para solicitar vinculação.',
            ]);
        }

        try {
            $response = $openCnpj->getCNPJ($cnpj);
            if (in_array($response['status'] ?? 0, [404, 429])) {
                return response()->json(['success' => false, 'message' => 'CNPJ não encontrado.'], 404);
            }
            $data = $response['data'] ?? [];
            return response()->json([
                'success'            => true,
                'exists_in_database' => false,
                'already_has_seller' => false,
                'store'              => [
                    'cnpj'       => $cnpj,
                    'legal_name' => $data['razao_social'] ?? '',
                    'name'       => $data['nome_fantasia'] ?? '',
                ],
            ]);
        } catch (\Exception) {
            return response()->json(['success' => false, 'message' => 'Erro ao consultar CNPJ.'], 404);
        }
    }

    public function confirmStep1(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj'       => 'required|regex:/^\d{14}$/',
            'legal_name' => 'required|string|max:255',
            'name'       => 'required|string|max:255',
        ]);

        return response()->json(['success' => true, 'message' => 'Dados confirmados!', 'data' => $request->only('cnpj','legal_name','name')]);
    }

    public function confirmStep2(Request $request): JsonResponse
    {
        $request->validate([
            'cnpj'             => 'required|regex:/^\d{14}$/',
            'address_cep'      => 'required|regex:/^\d{8}$/',
            'address_street'   => 'required|string|max:255',
            'address_number'   => 'required|string|max:20',
            'address_district' => 'required|string|max:100',
            'address_city'     => 'required|string|max:100',
            'address_state'    => 'required|regex:/^[A-Z]{2}$/',
        ]);

        return response()->json(['success' => true, 'message' => 'Endereço confirmado!', 'data' => $request->all()]);
    }

    public function confirmStep3(Request $request, GestaoClickService $gestaoClick): JsonResponse
    {
        $seller           = auth()->user()->seller;
        $existsInDatabase = $request->boolean('exists_in_database', false);
        $alreadyHasSeller = $request->boolean('already_has_seller', false);

        $rules = ['cnpj' => 'required|regex:/^\d{14}$/', 'legal_name' => 'required|string|max:255', 'name' => 'required|string|max:255'];
        if (!$existsInDatabase) {
            $rules = array_merge($rules, [
                'address_cep'      => 'required|regex:/^\d{8}$/',
                'address_street'   => 'required|string|max:255',
                'address_number'   => 'required|string|max:20',
                'address_district' => 'required|string|max:100',
                'address_city'     => 'required|string|max:100',
                'address_state'    => 'required|regex:/^[A-Z]{2}$/',
                'hours'            => 'required|array',
            ]);
        }

        $validated = $request->validate($rules);
        $cnpj      = preg_replace('/\D/', '', $request->cnpj);

        try {
            $result = DB::transaction(function () use ($cnpj, $validated, $seller, $gestaoClick, $existsInDatabase, $alreadyHasSeller) {
                $existing = Store::where('cnpj', $cnpj)->first();

                if ($existsInDatabase && $existing) {
                    if ($alreadyHasSeller) {
                        // Create a claim for admin approval
                        $alreadyClaimed = SellerStoreClaim::where('seller_id', $seller->id)
                            ->where('store_id', $existing->id)
                            ->whereIn('status', ['pending', 'approved'])
                            ->exists();

                        if ($alreadyClaimed) {
                            throw new \Exception('Você já solicitou ou possui vínculo com esta loja.');
                        }

                        SellerStoreClaim::create([
                            'seller_id' => $seller->id,
                            'store_id'  => $existing->id,
                            'status'    => 'pending',
                        ]);

                        return ['store' => $existing, 'claim' => true];
                    } else {
                        // Store exists but has no seller — assign directly as pending approval
                        if ($existing->seller_id !== null) {
                            throw new \Exception('Esta loja já passou a ter vendedor. Solicite aprovação ao admin.');
                        }
                        $existing->update([
                            'seller_id'                => $seller->id,
                            'seller_assignment_status' => 'pending',
                        ]);
                        return ['store' => $existing, 'claim' => false];
                    }
                }

                // New store — create in GestaoClick and DB
                $gcStore = $gestaoClick->firstOrCreateStore($validated);
                $gestaoClickId = null;

                if (isset($gcStore['data'][0]['id'])) {
                    $gestaoClickId = $gcStore['data'][0]['id'];
                } elseif (isset($gcStore['data']['id'])) {
                    $gestaoClickId = $gcStore['data']['id'];
                }

                if ($gestaoClickId === null) {
                    \Illuminate\Support\Facades\Log::error('Unexpected GestaoClick store response format.', [
                        'cnpj' => $cnpj,
                        'response' => $gcStore,
                    ]);

                    throw new \UnexpectedValueException('Resposta inesperada ao criar loja no GestaoClick.');
                }
                $store = Store::firstOrNew(['cnpj' => $cnpj]);
                $store->gestao_click_id = $gestaoClickId;
                $store->fill([
                    'name'               => $validated['name'],
                    'legal_name'         => $validated['legal_name'],
                    'address_cep'        => $validated['address_cep'],
                    'address_street'     => $validated['address_street'],
                    'address_number'     => $validated['address_number'],
                    'address_complement' => $validated['address_complement'] ?? '',
                    'address_district'   => $validated['address_district'],
                    'address_city'       => $validated['address_city'],
                    'address_state'      => $validated['address_state'],
                    'seller_id'                => $seller->id,
                    'seller_assignment_status' => 'pending',
                ]);
                $store->save();

                $store->storeHours()->delete();
                $hoursData = [];
                foreach ($validated['hours'] as $day => $hourData) {
                    $hoursData[] = [
                        'day_of_week' => $day,
                        'open_time'   => $hourData['is_open'] ? $hourData['open_time'] : null,
                        'close_time'  => $hourData['is_open'] ? $hourData['close_time'] : null,
                        'is_open'     => $hourData['is_open'] ?? true,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
                if (!empty($hoursData)) {
                    $store->storeHours()->createMany($hoursData);
                }

                return ['store' => $store, 'claim' => false];
            });

            $isClaim = $result['claim'];
            return response()->json([
                'success' => true,
                'is_claim' => $isClaim,
                'message' => $isClaim
                    ? 'Solicitação de vínculo enviada! Aguarde aprovação do admin.'
                    : 'Loja cadastrada! Aguardando aprovação do admin para ativação das comissões.',
                'store' => $result['store'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function edit(Store $store)
    {
        $seller = auth()->user()->seller;
        abort_if($store->seller_id !== $seller->id, 403);
        abort_if($store->hasLinkedClients(), 403, 'Não é possível editar loja com clientes vinculados.');

        $storeHours = $store->storeHours()->orderBy('day_of_week')->get()->keyBy('day_of_week');
        return view('seller.stores.edit', compact('store', 'storeHours'));
    }

    public function update(Request $request, Store $store)
    {
        $seller = auth()->user()->seller;
        abort_if($store->seller_id !== $seller->id, 403);
        abort_if($store->hasLinkedClients(), 403, 'Não é possível editar loja com clientes vinculados.');

        $request->merge(['address_cep' => preg_replace('/\D/', '', $request->address_cep ?? '')]);

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'address_cep'        => 'required|regex:/^\d{8}$/',
            'address_street'     => 'required|string|max:255',
            'address_number'     => 'required|string|max:20',
            'address_complement' => 'nullable|string|max:255',
            'address_district'   => 'required|string|max:100',
            'address_city'       => 'required|string|max:100',
            'address_state'      => 'required|regex:/^[A-Z]{2}$/',
            'hours'              => 'required|array',
        ]);

        DB::transaction(function () use ($store, $validated) {
            $store->update([
                'name'               => $validated['name'],
                'address_cep'        => $validated['address_cep'],
                'address_street'     => $validated['address_street'],
                'address_number'     => $validated['address_number'],
                'address_complement' => $validated['address_complement'] ?? '',
                'address_district'   => $validated['address_district'],
                'address_city'       => $validated['address_city'],
                'address_state'      => $validated['address_state'],
            ]);

            $store->storeHours()->delete();
            $hoursData = [];
            foreach ($validated['hours'] as $day => $hourData) {
                $isOpen = ($hourData['is_open'] ?? '0') === '1';
                $hoursData[] = [
                    'day_of_week' => $day,
                    'open_time'   => $isOpen ? ($hourData['open_time'] ?? null) : null,
                    'close_time'  => $isOpen ? ($hourData['close_time'] ?? null) : null,
                    'is_open'     => $isOpen,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            if (!empty($hoursData)) {
                $store->storeHours()->createMany($hoursData);
            }
        });

        return redirect()->route('seller.stores.index')->with('success', 'Loja atualizada com sucesso!');
    }
}
