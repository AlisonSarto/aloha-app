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
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(5)
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
