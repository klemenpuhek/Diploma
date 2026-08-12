<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Reservation::orderBy('date')->get()
        );
    }

    public function availableSlots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'court' => ['required', 'string'],
            'date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'in:1,2'],
        ]);

        $court = Court::find($data['court']);

        if (! $court) {
            abort(404, 'Igrišče ne obstaja.');
        }

        $duration = (int) $data['duration'];
        $date = Carbon::parse($data['date']);
        $busy = $this->busyIntervals($court, $date, $request->query('exclude'));

        $slots = [];

        for ($start = Reservation::OPEN_HOUR; $start + $duration <= Reservation::CLOSE_HOUR; $start++) {
            $end = $start + $duration;

            if (! $this->hasOverlap($busy, $start, $end)) {
                $slots[] = [
                    'startingHour' => sprintf('%02d:00', $start),
                    'endingHour' => sprintf('%02d:00', $end),
                ];
            }
        }

        return response()->json(['slots' => $slots]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'court' => ['required', 'string'],
            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'in:1,2'],
            'startingHour' => ['required', 'integer', 'min:' . Reservation::OPEN_HOUR, 'max:' . (Reservation::CLOSE_HOUR - 1)],
        ]);

        $court = Court::find($data['court']);

        if (! $court) {
            abort(404, 'Igrišče ne obstaja.');
        }

        $duration = (int) $data['duration'];
        $start = (int) $data['startingHour'];
        $end = $start + $duration;

        if ($end > Reservation::CLOSE_HOUR) {
            return back()->withErrors(['startingHour' => 'Izbrani termin ni veljaven.'])->withInput();
        }

        $date = Carbon::parse($data['date']);
        $busy = $this->busyIntervals($court, $date);

        if ($this->hasOverlap($busy, $start, $end)) {
            return back()->withErrors(['startingHour' => 'Izbrani termin je že zaseden.'])->withInput();
        }

        Reservation::create([
            'court' => $court->getKey(),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'date' => $date,
            'startingHour' => sprintf('%02d:00', $start),
            'endingHour' => sprintf('%02d:00', $end),
        ]);

        return back()->with('success', 'Rezervacija uspešno ustvarjena.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $reservation = Reservation::findOrFail($id);

        $data = $request->validate([
            'court' => ['required', 'string'],
            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'date' => ['required', 'date'],
            'duration' => ['required', 'integer', 'in:1,2'],
            'startingHour' => ['required', 'integer', 'min:' . Reservation::OPEN_HOUR, 'max:' . (Reservation::CLOSE_HOUR - 1)],
        ]);

        $court = Court::find($data['court']);

        if (! $court) {
            abort(404, 'Igrišče ne obstaja.');
        }

        $duration = (int) $data['duration'];
        $start = (int) $data['startingHour'];
        $end = $start + $duration;

        if ($end > Reservation::CLOSE_HOUR) {
            return back()->withErrors(['startingHour' => 'Izbrani termin ni veljaven.'])->withInput();
        }

        $date = Carbon::parse($data['date']);
        $busy = $this->busyIntervals($court, $date, $reservation->id);

        if ($this->hasOverlap($busy, $start, $end)) {
            return back()->withErrors(['startingHour' => 'Izbrani termin je že zaseden.'])->withInput();
        }

        $reservation->update([
            'court' => $court->getKey(),
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'date' => $date,
            'startingHour' => sprintf('%02d:00', $start),
            'endingHour' => sprintf('%02d:00', $end),
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Rezervacija posodobljena.');
    }

    public function destroy(string $id): RedirectResponse
    {
        Reservation::findOrFail($id)->delete();

        return redirect()->route('admin.dashboard');
    }

    /**
     * @return array<int, array{start: int, end: int}>
     */
    private function busyIntervals(Court $court, Carbon $date, ?string $excludeId = null): array
    {
        return Reservation::where('court', new ObjectId($court->getKey()))
            ->whereBetween('date', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->get()
            ->map(fn (Reservation $reservation) => [
                'start' => Reservation::normalizeHour($reservation->startingHour),
                'end' => Reservation::normalizeHour($reservation->endingHour),
            ])
            ->all();
    }

    /**
     * @param array<int, array{start: int, end: int}> $busy
     */
    private function hasOverlap(array $busy, int $start, int $end): bool
    {
        return collect($busy)->contains(
            fn (array $interval) => $start < $interval['end'] && $end > $interval['start']
        );
    }
}
