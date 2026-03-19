<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryConfig;
use Illuminate\Http\Request;

class DeliveryConfigController extends Controller
{
    public function edit()
    {
        $config = DeliveryConfig::current();

        return view('admin.delivery-config.edit', compact('config'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'delivery_days'   => 'required|array|min:1',
            'delivery_days.*' => 'integer|between:0,6',
            'lead_days'       => 'required|integer|min:0|max:30',
            'late_direction'  => 'required|in:before,after',
        ]);

        DeliveryConfig::current()->update([
            'delivery_days'  => $validated['delivery_days'],
            'lead_days'      => $validated['lead_days'],
            'late_direction' => $validated['late_direction'],
        ]);

        return back()->with('success', 'Configurações de entrega salvas com sucesso!');
    }
}
