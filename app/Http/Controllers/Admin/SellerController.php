<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SellerController extends Controller
{
    public function index(Request $request) {
        $search = $request->get('search');

        $sellers = Seller::with('user')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhere('phone', 'like', '%' . $search . '%');
            })
            ->paginate(10);

        return view('admin.sellers.index', compact('sellers', 'search'));
    }

    public function create()
    {
        return view('admin.sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:sellers,phone',
            'commission_new_client' => 'required|numeric|min:0|max:100',
            'commission_recurring' => 'required|numeric|min:0|max:100',
            'monthly_package_target' => 'required|integer|min:0',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('seller');

        Seller::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
            'commission_new_client' => $request->commission_new_client,
            'commission_recurring' => $request->commission_recurring,
            'monthly_package_target' => $request->monthly_package_target,
        ]);

        return redirect()->route('admin.sellers.index')->with('success', 'Vendedor criado com sucesso.');
    }

    public function show(Seller $seller)
    {
        $seller->load('user');
        return view('admin.sellers.show', compact('seller'));
    }

    public function edit(Seller $seller)
    {
        return view('admin.sellers.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($seller->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'required|string|max:20|unique:sellers,phone,' . $seller->id,
            'commission_new_client' => 'required|numeric|min:0|max:100',
            'commission_recurring' => 'required|numeric|min:0|max:100',
            'monthly_package_target' => 'required|integer|min:0',
        ]);

        $seller->user->name = $request->name;
        $seller->user->email = $request->email;

        if ($request->filled('password')) {
            $seller->user->password = Hash::make($request->password);
        }

        $seller->user->save();

        $seller->update([
            'phone' => $request->phone,
            'commission_new_client' => $request->commission_new_client,
            'commission_recurring' => $request->commission_recurring,
            'monthly_package_target' => $request->monthly_package_target,
        ]);

        return redirect()->route('admin.sellers.show', $seller)->with('success', 'Vendedor atualizado com sucesso.');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();

        return redirect()->route('admin.sellers.index')->with('success', 'Vendedor excluído com sucesso.');
    }
}
