<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdmiController extends Controller
{
    public function index()
    {
        return view('PageAdmi.Dashboard');
    }
}
