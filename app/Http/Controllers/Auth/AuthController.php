<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Services\GestaoClickService;
use App\Models\User;
use App\Models\Store;
use App\Models\Client;

class AuthController extends Controller
{
    public function form() {
        if (auth()->check()) {
            return redirect()->route('home');
        }
        return view('auth.index');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');
        $remember = true;

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->route('home');
        }

        return back()->withErrors(['error' => 'Credenciais inválidas']);
    }

    public function registerForm() {
        if (auth()->check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request, GestaoClickService $gestaoClick) {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('client');

        Auth::login($user);

        $client = Client::create([
            'user_id' => $user->id,
            'phone' => $request->phone,
        ]);

        return redirect()->route('client.stores.register');

    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
