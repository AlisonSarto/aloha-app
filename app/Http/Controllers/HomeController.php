<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {

        if (Auth::check()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.home');
            }
        }

        return view('index');
    }
}
