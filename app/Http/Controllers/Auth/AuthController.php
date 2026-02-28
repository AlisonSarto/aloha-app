<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
