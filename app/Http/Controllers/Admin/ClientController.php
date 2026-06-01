<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Store;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $clients = Client::when($search, function ($query) use ($search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.clients.index', compact('clients', 'search'));
    }

    /**
     * Show form to link stores to a client.
     */
    public function show(Client $client)
    {
        $stores = Store::orderBy('name')->get();
        $client->load('stores');

        return view('admin.clients.show', compact('client', 'stores'));
    }

    /**
     * Search for stores by CNPJ or CPF (AJAX).
     */
    public function searchStores(Request $request)
    {
        $search = $request->query('q', '');
        $search = trim(preg_replace('/\D/', '', $search));

        if (empty($search)) {
            return response()->json(['stores' => []]);
        }

        $stores = Store::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    // Search by CNPJ (14 digits)
                    if (strlen($search) === 14) {
                        $q->where('cnpj', 'like', "%{$search}%");
                    }
                    // Search by CPF (11 digits)
                    if (strlen($search) === 11) {
                        $q->orWhere('cpf', 'like', "%{$search}%");
                    }
                    // Also search by name
                    $q->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'identifier' => formatIdentifier($store),
                    'cnpj' => $store->cnpj,
                    'cpf' => $store->cpf,
                ];
            });

        return response()->json(['stores' => $stores]);
    }

    /**
     * Update the stores linked to the client.
     */
    public function updateStores(Request $request, Client $client)
    {
        $client->stores()->sync($request->stores ?? []);

        return redirect()
            ->route('admin.clients.show', $client)
            ->with('success', 'Comércios vinculados atualizados.');
    }
}
