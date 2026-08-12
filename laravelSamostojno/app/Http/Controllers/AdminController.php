<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function loadData(Request $request): View
    {
        $courts = Court::all()->sortBy('number');

        $reservations = Reservation::with('courtModel')->get();

        $filterCourt = $request->query('filterCourt');
        $filterSearch = trim((string) $request->query('filterSearch'));

        if ($filterCourt) {
            $reservations = $reservations->filter(
                fn($r) => (string) $r->court === (string) $filterCourt
            );
        }

        if ($filterSearch !== '') {
            $search = mb_strtolower($filterSearch);
            $reservations = $reservations->filter(
                fn($r) => str_contains(mb_strtolower($r->name), $search)
                || str_contains(mb_strtolower($r->surname), $search)
            );
        }

        $reservations = $reservations->sortBy('date');

        $editingCourt = null;
        if ($request->query('edit')) {
            $editingCourt = Court::find($request->query('edit'));
        }

        $editingReservation = null;
        $editingReservationDuration = null;
        if ($request->query('editReservation')) {
            $editingReservation = Reservation::find($request->query('editReservation'));

            if ($editingReservation) {
                $start = (int) explode(':', $editingReservation->startingHour)[0];
                $end = (int) explode(':', $editingReservation->endingHour)[0];
                $editingReservationDuration = $end - $start;
            }
        }

        return view('adminPanel', [
            'courts' => $courts,
            'reservations' => $reservations,
            'editingCourt' => $editingCourt,
            'editingReservation' => $editingReservation,
            'editingReservationDuration' => $editingReservationDuration,
            'filterCourt' => $filterCourt,
            'filterSearch' => $filterSearch,
        ]);
    }

    // ----------------courts---------------

    public function addCourt(Request $request): RedirectResponse
    {

        $data = $request->validate([
            'number' => ['required', 'integer'],
            'imagePath' => ['required', 'string']
        ]);

        Court::create($data);

        return redirect()->route('adminPanel');
    }

    public function deleteCourt(string $id): RedirectResponse
    {

        Court::findOrFail($id)->delete();
        return redirect()->route('adminPanel');
    }

    public function editCourt(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer'],
            'imagePath' => ['required', 'string'],
        ]);

        Court::findOrFail($id)->update($data);

        return redirect()->route('adminPanel');
    }

    //-----------reservations---------------

    public function deleteReservation(string $id): RedirectResponse
    {
        Reservation::findOrFail($id)->delete();
        return redirect()->route('adminPanel');
    }
    public function addReservation(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'court' => ['required', 'string'],
            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'in:1,2'],
            'startingHour' => ['required', 'integer', 'min:8', 'max:21'],
        ]);

        $court = Court::findOrFail($data['court']);

        $duration = (int) $data['duration'];
        $start = (int) $data['startingHour'];
        $end = $start + $duration;

        Reservation::create([
            'court' => $court->id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'date' => $data['date'],
            'startingHour' => sprintf('%02d:00', $start),
            'endingHour' => sprintf('%02d:00', $end),
        ]);

        return redirect()->route('adminPanel');
    }

    public function editReservation(Request $request, string $id): RedirectResponse
    {
        $data = $request->validate([
            'court' => ['required', 'string'],
            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'in:1,2'],
            'startingHour' => ['required', 'integer', 'min:8', 'max:21'],
        ]);

        $court = Court::findOrFail($data['court']);

        $duration = (int) $data['duration'];
        $start = (int) $data['startingHour'];
        $end = $start + $duration;

        Reservation::findOrFail($id)->update([
            'court' => $court->id,
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'date' => $data['date'],
            'startingHour' => sprintf('%02d:00', $start),
            'endingHour' => sprintf('%02d:00', $end),
        ]);

        return redirect()->route('adminPanel');
    }
}