<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceTable;
use App\Models\PriceTableRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceTableController extends Controller
{
    public function index(Request $request)
    {
        $priceTables = PriceTable::orderBy('is_default', 'desc')->paginate(10);

        return view('admin.price-tables.index', compact('priceTables'));
    }

    public function create()
    {
        return view('admin.price-tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'ranges' => 'required|array|min:1',
            'ranges.*.min_quantity' => 'required|integer|min:1',
            'ranges.*.max_quantity' => 'nullable|integer|min:1',
            'ranges.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($request->is_default === null) {
            $request->is_default = false;
        }

        DB::transaction(function () use ($request) {
            if ($request->is_default) {
                PriceTable::where('is_default', true)->update(['is_default' => false]);
            }

            $priceTable = PriceTable::create([
                'name' => $request->name,
                'is_default' => $request->is_default,
            ]);

            foreach ($request->ranges as $range) {
                PriceTableRange::create([
                    'price_table_id' => $priceTable->id,
                    'min_quantity' => $range['min_quantity'],
                    'max_quantity' => $range['max_quantity'],
                    'unit_price' => $range['unit_price'],
                ]);
            }
        });

        return redirect()->route('admin.price-tables.index')->with('success', 'Tabela de preços criada com sucesso.');
    }

    public function show(PriceTable $priceTable)
    {
        return view('admin.price-tables.show', compact('priceTable'));
    }

    public function edit(PriceTable $priceTable)
    {
        return view('admin.price-tables.edit', compact('priceTable'));
    }

    public function update(Request $request, PriceTable $priceTable)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_default' => 'boolean',
            'ranges' => 'required|array|min:1',
            'ranges.*.min_quantity' => 'required|integer|min:1',
            'ranges.*.max_quantity' => 'nullable|integer|min:1',
            'ranges.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $priceTable) {
            if ($request->is_default && !$priceTable->is_default) {
                PriceTable::where('is_default', true)->update(['is_default' => false]);
            }

            $priceTable->update([
                'name' => $request->name,
                'is_default' => $request->is_default,
            ]);

            // Delete existing ranges
            $priceTable->ranges()->delete();

            // Create new ranges
            foreach ($request->ranges as $range) {
                PriceTableRange::create([
                    'price_table_id' => $priceTable->id,
                    'min_quantity' => $range['min_quantity'],
                    'max_quantity' => $range['max_quantity'],
                    'unit_price' => $range['unit_price'],
                ]);
            }
        });

        return redirect()->route('admin.price-tables.index')->with('success', 'Tabela de preços atualizada com sucesso.');
    }

    public function destroy(PriceTable $priceTable)
    {
        if ($priceTable->is_default) {
            return redirect()->route('admin.price-tables.index')->with('error', 'Não é possível deletar a tabela padrão.');
        }

        $priceTable->delete();

        return redirect()->route('admin.price-tables.index')->with('success', 'Tabela de preços deletada com sucesso.');
    }

    public function setDefault(PriceTable $priceTable)
    {
        PriceTable::where('is_default', true)->update(['is_default' => false]);
        $priceTable->update(['is_default' => true]);

        return redirect()->route('admin.price-tables.index')->with('success', 'Tabela padrão definida com sucesso.');
    }
}
