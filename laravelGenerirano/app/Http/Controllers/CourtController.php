<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\JsonResponse;

class CourtController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Court::orderBy('number')->get(['id', 'number', 'imagePath'])
        );
    }
}
