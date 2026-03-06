<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\PriceTable;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $stores = Store::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('admin.stores.index', compact('stores', 'search'));
    }

    public function show(Store $store) {
        return view('admin.stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        $priceTables = PriceTable::all();

        return view('admin.stores.edit', compact('store', 'priceTables'));
    }

    public function update(Request $request, Store $store)
    {
        $store->update($request->all());

        return redirect()->route('admin.stores.index');
    }
}
