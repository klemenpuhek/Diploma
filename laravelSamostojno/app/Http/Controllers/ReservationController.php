<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\Court;
use App\Models\Reservation;
use MongoDB\BSON\ObjectId;

class ReservationController extends Controller
{
    private function mapirajRezervacije($reservations): \Illuminate\Support\Collection
    {
        return $reservations->map(fn($r) => [
            (int) explode(':', $r->startingHour)[0],
            (int) explode(':', $r->endingHour)[0],
        ]);
    }

    private function pridobiVseMapiraneUre(): array
    {
        $mapiraneEnoUrne = [];
        for ($i = 8; $i < 22; $i++) {
            $mapiraneEnoUrne[] = [$i, $i + 1];
        }

        $mapiraneDvoUrne = [];
        for ($i = 8; $i < 21; $i += 2) {
            $mapiraneDvoUrne[] = [$i, $i + 2];
        }
        for ($i = 9; $i < 21; $i += 2) {
            $mapiraneDvoUrne[] = [$i, $i + 2];
        }

        return [$mapiraneEnoUrne, $mapiraneDvoUrne];
    }

    private function pridobiUgodneRezervacije($zasedeneRezervacije): array
    {
        [$mapiraneEnoUrne, $mapiraneDvoUrne] = $this->pridobiVseMapiraneUre();

        $jeProsto = function ($termin) use ($zasedeneRezervacije) {

            foreach ($zasedeneRezervacije as $z) {
                $obstajaPrekrivanje = $termin[0] < $z[1] && $z[0] < $termin[1];

                if ($obstajaPrekrivanje) {
                    return false;
                }
            }

            return true;
        };

        return [
            'mapiraneEnoUrne' => array_values(array_filter($mapiraneEnoUrne, $jeProsto)),
            'mapiraneDvoUrne' => array_values(array_filter($mapiraneDvoUrne, $jeProsto)),
        ];
    }

    private function pridobiMozneRezervacije($reservations, $datum): array
    {
        $rezervacijeNaIstiDan = $reservations->filter(
            fn($r) => \Carbon\Carbon::parse($r->date)->isSameDay(\Carbon\Carbon::parse($datum))
        );

        $zasedeneUre = $this->mapirajRezervacije($rezervacijeNaIstiDan);

        return $this->pridobiUgodneRezervacije($zasedeneUre);
    }

    public function vseMoznosti(Request $request, string $id): JsonResponse{

        $datum = $request->query('date');
        $court = Court::findOrFail($id);

        $reservation = Reservation::where('court', new ObjectId($court->id))->get();

        $termini = $this->pridobiMozneRezervacije($reservation, $datum);

        return response()->json($termini);
    }
    public function addReservationHome(Request $request): RedirectResponse
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

        return redirect()->route('loadDataHome');
    }
}
