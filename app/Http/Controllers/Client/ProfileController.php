<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('client.profile.index');
    }

    public function update(Request $request)
    {
        $client = Auth::user()->client;

        $request->validateWithBag('edit_profile', [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = preg_replace('/\D/', '', $request->phone);
        if (strlen($phone) === 11) {
            $phone = '55' . $phone;
        }

        Auth::user()->update($request->only('name', 'email'));
        $client->update(['phone' => $phone]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $request->validateWithBag('change_password', [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('delete_account', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Conta excluída com sucesso.');
    }
}
