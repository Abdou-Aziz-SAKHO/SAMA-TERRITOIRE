<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartographieController extends Controller
{
    /**
     * Affiche la page cartographie.
     */
    public function index()
    {
        return view('PageUser.cartographie');
    }
    public function climat()
    {
        return view('PageUser.climat');
    }
    public function statistique()
    {
        return view('PageUser.statistique');
    }
}
