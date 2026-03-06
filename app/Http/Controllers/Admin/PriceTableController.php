<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PriceTableController extends Controller
{
    public function index() {
        return view('admin.price-tables.index');
    }
}
