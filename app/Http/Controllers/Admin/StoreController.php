<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\PriceTable;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $stores = Store::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.stores.index', compact('stores', 'search'));
    }

    public function show(Store $store) {
        $store->load('storeHours');

        return view('admin.stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        $store->load('storeHours');
        $priceTables = PriceTable::all();

        return view('admin.stores.edit', compact('store', 'priceTables'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'shipping_amount'    => 'required|numeric|min:0',
            'price_table_id'     => 'nullable|exists:price_tables,id',
            'boleto_due_days'    => 'required|integer|min:1',
            'address_cep'        => 'nullable|string|max:20',
            'address_street'     => 'nullable|string|max:255',
            'address_number'     => 'nullable|string|max:20',
            'address_complement' => 'nullable|string|max:255',
            'address_district'   => 'nullable|string|max:100',
            'address_city'       => 'nullable|string|max:100',
            'address_state'      => 'nullable|string|max:2',
            'hours'              => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $store, $validated) {
            $store->update([
                'name'               => $validated['name'],
                'shipping_amount'    => $validated['shipping_amount'],
                'price_table_id'     => $validated['price_table_id'] ?? null,
                'can_use_boleto'     => $request->boolean('can_use_boleto'),
                'boleto_due_days'    => $validated['boleto_due_days'],
                'address_cep'        => $validated['address_cep'] ?? null,
                'address_street'     => $validated['address_street'] ?? null,
                'address_number'     => $validated['address_number'] ?? null,
                'address_complement' => $validated['address_complement'] ?? null,
                'address_district'   => $validated['address_district'] ?? null,
                'address_city'       => $validated['address_city'] ?? null,
                'address_state'      => $validated['address_state'] ?? null,
            ]);

            if (!empty($validated['hours'])) {
                $store->storeHours()->delete();

                $hoursData = [];
                foreach ($validated['hours'] as $dayOfWeek => $hourData) {
                    $isOpen = ($hourData['is_open'] ?? '0') === '1';
                    $hoursData[] = [
                        'day_of_week' => $dayOfWeek,
                        'open_time'   => $isOpen ? ($hourData['open_time'] ?? null) : null,
                        'close_time'  => $isOpen ? ($hourData['close_time'] ?? null) : null,
                        'is_open'     => $isOpen,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                $store->storeHours()->createMany($hoursData);
            }
        });

        return redirect()->route('admin.stores.show', $store)->with('success', 'Comércio atualizado com sucesso!');
    }
}
