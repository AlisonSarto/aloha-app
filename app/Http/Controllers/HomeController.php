<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {

        if (Auth::check()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.home.index');
            }
            if (auth()->user()->hasRole('client')) {
                return redirect()->route('client.orders.create');
            }
            if (auth()->user()->hasRole('seller')) {
                return redirect()->route('seller.home');
            }
            if (auth()->user()->hasRole('erp')) {
                return redirect()->route('erp.home.index');
            }
        }

        return view('index');
    }
}
