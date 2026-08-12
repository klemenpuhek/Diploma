<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use MongoDB\BSON\ObjectId;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $courts = Court::orderBy('number')->get();
        $courts->each(function (Court $court) {
            $court->reservationsCount = $court->reservations()->count();
        });

        $search = trim((string) $request->query('search'));
        $courtId = $request->query('court');

        $reservations = Reservation::with('courtModel')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('surname', 'like', '%' . $search . '%');
                });
            })
            ->when($courtId, fn ($query) => $query->where('court', new ObjectId($courtId)))
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', [
            'courts' => $courts,
            'reservations' => $reservations,
            'search' => $search,
            'selectedCourt' => $courtId,
        ]);
    }
}
