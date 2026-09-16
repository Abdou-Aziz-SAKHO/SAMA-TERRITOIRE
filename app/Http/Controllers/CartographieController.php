<?php

namespace App\Http\Controllers;

use App\Http\Controllers\StatsAdmiController;
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
    public function statistique(Request $request)
    {
        $data = app(StatsAdmiController::class)->vueGeneraleData($request);

        return view('PageUser.statistique', $data);
    }
}
