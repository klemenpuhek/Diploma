<!DOCTYPE html>
<html lang="sl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>

    <header id="header">
        <h1>Admin panel</h1>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="logoutButton" id="logoutButton">Odjava</button>
        </form>
    </header>

    <section class="cardsBody">
        <div class="card">
            <h2>Igrišča</h2>

            <form action="{{ $editingCourt ? route('editCourt', $editingCourt->id) : route('addCourt') }}" method="POST"
                class="courtForm">
                @csrf
                @if ($editingCourt)
                    @method('PUT')
                @endif

                <input type="number" name="number" placeholder="Številka igrišča"
                    value="{{ old('number', $editingCourt->number ?? '') }}" required>
                <input type="text" name="imagePath" placeholder="Pot do slike"
                    value="{{ old('imagePath', $editingCourt->imagePath ?? '') }}" required>

                <button type="submit" id="courtSubmit">
                    {{ $editingCourt ? 'Shrani spremembe' : 'Dodaj igrišče' }}
                </button>
            </form>

            <table class="courtsTable">
                <thead>
                    <tr>
                        <th>Številka igrišča</th>
                        <th>Slika</th>
                        <th>Št. rezervacij</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courts as $court)
                        <tr>
                            <td>{{ $court->number }}</td>
                            <td>{{ $court->imagePath }}</td>
                            <td>{{ $court->reservations()->count() }}</td>
                            <td>
                                <a href="{{ route('adminPanel', ['edit' => $court->id]) }}" class="editButton">Uredi</a>

                                <form action="{{ route('deleteCourt', $court->id) }}" method="POST" class="inlineForm">
                                    @csrf
                                    @method('DELETE')
                                    <button class="deleteButton">Izbriši</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Rezervacije</h2>

            <form
                action="{{ $editingReservation ? route('editReservation', $editingReservation->id) : route('addReservation') }}"
                method="POST" class="reservationForm">
                @csrf
                @if ($editingReservation)
                    @method('PUT')
                @endif

                <select name="court" id="reservationCourt">
                    <option value="">Izberi igrišče</option>
                    @foreach ($courts as $court)
                        <option value="{{ $court->id }}" @selected((string) optional($editingReservation)->court === (string) $court->id)>
                            Igrišče {{ $court->number }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="name" placeholder="Ime"
                    value="{{ old('name', $editingReservation->name ?? '') }}">
                <input type="text" name="surname" placeholder="Priimek"
                    value="{{ old('surname', $editingReservation->surname ?? '') }}">
                <input type="email" name="email" placeholder="Email"
                    value="{{ old('email', $editingReservation->email ?? '') }}">
                <input type="date" name="date" id="reservationDate"
                    value="{{ old('date', $editingReservation ? \Carbon\Carbon::parse($editingReservation->date)->format('Y-m-d') : '') }}">

                <select name="duration" id="reservationDuration">
                    <option value="1" @selected($editingReservationDuration === 1)>1 ura</option>
                    <option value="2" @selected($editingReservationDuration === 2)>2 uri</option>
                </select>

                <select name="startingHour" id="reservationStart">
                    @if ($editingReservation)
                        @php $trenutnaUra = (int) explode(':', $editingReservation->startingHour)[0]; @endphp
                        @for ($i = 8; $i <= 21 - $editingReservationDuration; $i++)
                            <option value="{{ $i }}" @selected($i === $trenutnaUra)>
                                {{ sprintf('%02d:00', $i) }} - {{ sprintf('%02d:00', $i + $editingReservationDuration) }}
                            </option>
                        @endfor
                    @else
                        <option value="">Najprej izberi igrišče in datum</option>
                    @endif
                </select>

                <button type="submit"
                    id="reservationSubmit">{{ $editingReservation ? 'Shrani spremembe' : 'Dodaj rezervacijo' }}</button>
            </form>

            <div class="filters">
                <form action="{{ route('adminPanel') }}" method="GET" class="filters">
                    <select name="filterCourt">
                        <option value="">Vsa igrišča</option>
                        @foreach ($courts as $court)
                            <option value="{{ $court->id }}" @selected($filterCourt == $court->id)>
                                Igrišče {{ $court->number }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="filterSearch" placeholder="Išči po imenu ali priimku"
                        value="{{ $filterSearch }}">
                    <button class="filterButton" type="submit">Filtriraj</button>
                </form>
            </div>
            <table class="reservationTable">
                <thead>
                    <tr>
                        <th>Igrišče</th>
                        <th>Ime</th>
                        <th>Priimek</th>
                        <th>Email</th>
                        <th>Datum</th>
                        <th>Ura</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reservations as $reservation)
                        <tr>
                            <td>Igrišče {{ $reservation->courtModel->number ?? '-' }}</td>
                            <td>{{ $reservation->name }}</td>
                            <td>{{ $reservation->surname }}</td>
                            <td>{{ $reservation->email }}</td>
                            <td>{{ \Carbon\Carbon::parse($reservation->date)->format('d.m.Y') }}</td>
                            <td>{{ $reservation->startingHour }} - {{ $reservation->endingHour }}</td>
                            <td>
                                <a href="{{ route('adminPanel', ['editReservation' => $reservation->id]) }}"
                                    class="editButton">Uredi</a>
                                <form action="{{ route('deleteReservation', $reservation->id) }}" method="POST"
                                    class="inlineForm">
                                    @csrf
                                    @method('DELETE')
                                    <button class="deleteButton">Izbriši</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <script src="{{ asset('js/adminPanel.js') }}"></script>
</body>

</html>