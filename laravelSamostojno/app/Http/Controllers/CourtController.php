<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\View\View;

class CourtController extends Controller
{
    public function loadDataHome(): View
    {
        $courts = Court::all()->sortBy('number');

        return view('Home', [
            'courts' => $courts,
        ]);
    }
}