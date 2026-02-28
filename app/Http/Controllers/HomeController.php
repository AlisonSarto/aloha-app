<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() {
        if (auth()->check() === false) {
            return redirect()->route('login');
        }

        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.home');
        }else {
            return redirect()->route('logout');
        }

    }
}
