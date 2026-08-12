<!DOCTYPE html>
<html lang="sl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin panel</title>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    </head>
    <body>
        <nav class="admin-navbar">
            <span>Admin panel</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn-link">Odjava</button>
            </form>
        </nav>

        <main class="admin-content">
            <div class="admin-columns">
                <div class="admin-column">
                    <div class="admin-card">
                        <h2 id="court-form-title">Dodaj igrišče</h2>

                        @if ($errors->any() && old('number') !== null)
                            <div class="admin-error">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.courts.store') }}" id="court-form">
                            @csrf
                            <input type="hidden" name="_method" id="court-method" value="POST">

                            <div class="admin-field">
                                <label for="number">Številka igrišča</label>
                                <input id="number" type="number" name="number" value="{{ old('number') }}" required>
                            </div>

                            <div class="admin-field">
                                <label for="imagePath">Pot do slike</label>
                                <input id="imagePath" type="text" name="imagePath" value="{{ old('imagePath') }}" required>
                            </div>

                            <button type="submit" class="btn btn-block" id="court-submit">Dodaj</button>
                            <button type="button" class="btn-link" id="court-cancel" style="display:none">Prekliči urejanje</button>
                        </form>
                    </div>

                    <div class="admin-card">
                        <h2>Igrišča</h2>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Slika</th>
                                    <th>Številka</th>
                                    <th>Pot do slike</th>
                                    <th>Št. rezervacij</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($courts as $court)
                                    <tr>
                                        <td><img src="{{ $court->imagePath }}" alt="Igrišče {{ $court->number }}"></td>
                                        <td>{{ $court->number }}</td>
                                        <td>{{ $court->imagePath }}</td>
                                        <td>{{ $court->reservationsCount }}</td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn-link court-edit-btn"
                                                data-update-url="{{ route('admin.courts.update', $court->id) }}"
                                                data-number="{{ $court->number }}"
                                                data-image-path="{{ $court->imagePath }}"
                                            >Uredi</button>
                                            <form method="POST" action="{{ route('admin.courts.destroy', $court->id) }}" onsubmit="return confirm('Izbriši igrišče?');" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-link">Izbriši</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">Ni še dodanih igrišč.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-column">
                    <div class="admin-card">
                        <h2 id="res-form-title">Dodaj rezervacijo</h2>

                        @if (session('success'))
                            <div class="admin-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any() && old('name') !== null)
                            <div class="admin-error">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('reservations.store') }}" id="reservation-form">
                            @csrf
                            <input type="hidden" name="_method" id="res-method" value="POST">

                            <div class="admin-field">
                                <label for="res-court">Igrišče</label>
                                <select id="res-court" name="court" required>
                                    <option value="">Izberi igrišče</option>
                                    @foreach ($courts as $court)
                                        <option value="{{ $court->id }}">Igrišče {{ $court->number }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="admin-field">
                                <label for="res-date">Datum</label>
                                <input id="res-date" type="date" name="date" required>
                            </div>

                            <div class="admin-field">
                                <label for="res-duration">Trajanje</label>
                                <select id="res-duration" name="duration" required>
                                    <option value="1">1 ura</option>
                                    <option value="2">2 uri</option>
                                </select>
                            </div>

                            <div class="admin-field">
                                <label>Prosti termini</label>
                                <div id="res-slots" class="slots"></div>
                                <div id="res-slots-hint" class="slots-hint">Izberi igrišče in datum, da se prikažejo prosti termini.</div>
                                <input type="hidden" name="startingHour" id="res-starting-hour" required>
                            </div>

                            <div class="admin-field">
                                <label for="res-name">Ime</label>
                                <input id="res-name" type="text" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="admin-field">
                                <label for="res-surname">Priimek</label>
                                <input id="res-surname" type="text" name="surname" value="{{ old('surname') }}" required>
                            </div>

                            <div class="admin-field">
                                <label for="res-email">Email</label>
                                <input id="res-email" type="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <button type="submit" class="btn btn-block" id="res-submit" disabled>Dodaj rezervacijo</button>
                            <button type="button" class="btn-link" id="res-cancel" style="display:none">Prekliči urejanje</button>
                        </form>
                    </div>

                    <div class="admin-card">
                        <h2>Rezervacije</h2>

                        <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-row">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ime ali priimek">

                            <select name="court">
                                <option value="">Vsa igrišča</option>
                                @foreach ($courts as $court)
                                    <option value="{{ $court->id }}" @selected($selectedCourt == $court->id)>Igrišče {{ $court->number }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="btn">Filtriraj</button>
                            @if ($search !== '' || $selectedCourt)
                                <a href="{{ route('admin.dashboard') }}" class="btn-link">Počisti</a>
                            @endif
                        </form>

                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Igrišče</th>
                                    <th>Ime in priimek</th>
                                    <th>Email</th>
                                    <th>Datum</th>
                                    <th>Termin</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reservations as $reservation)
                                    <tr>
                                        <td>{{ $reservation->courtModel->number ?? '-' }}</td>
                                        <td>{{ $reservation->name }} {{ $reservation->surname }}</td>
                                        <td>{{ $reservation->email }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->date)->format('d.m.Y') }}</td>
                                        <td>{{ $reservation->startingHour }} - {{ $reservation->endingHour }}</td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn-link reservation-edit-btn"
                                                data-id="{{ $reservation->id }}"
                                                data-update-url="{{ route('admin.reservations.update', $reservation->id) }}"
                                                data-court="{{ $reservation->court }}"
                                                data-date="{{ \Carbon\Carbon::parse($reservation->date)->format('Y-m-d') }}"
                                                data-duration="{{ \App\Models\Reservation::normalizeHour($reservation->endingHour) - \App\Models\Reservation::normalizeHour($reservation->startingHour) }}"
                                                data-starting-hour="{{ sprintf('%02d:00', \App\Models\Reservation::normalizeHour($reservation->startingHour)) }}"
                                                data-name="{{ $reservation->name }}"
                                                data-surname="{{ $reservation->surname }}"
                                                data-email="{{ $reservation->email }}"
                                            >Uredi</button>
                                            <form method="POST" action="{{ route('admin.reservations.destroy', $reservation->id) }}" onsubmit="return confirm('Izbriši rezervacijo?');" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-link">Izbriši</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Ni še rezervacij.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <script>
            (function () {
                // Court form: toggle between "add" and "edit" mode.
                const courtForm = document.getElementById('court-form');
                const courtMethod = document.getElementById('court-method');
                const courtTitle = document.getElementById('court-form-title');
                const courtSubmit = document.getElementById('court-submit');
                const courtCancel = document.getElementById('court-cancel');
                const courtNumberInput = document.getElementById('number');
                const courtImageInput = document.getElementById('imagePath');
                const courtStoreUrl = courtForm.action;

                function resetCourtForm() {
                    courtForm.reset();
                    courtForm.action = courtStoreUrl;
                    courtMethod.value = 'POST';
                    courtTitle.textContent = 'Dodaj igrišče';
                    courtSubmit.textContent = 'Dodaj';
                    courtCancel.style.display = 'none';
                }

                document.querySelectorAll('.court-edit-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        courtForm.action = btn.dataset.updateUrl;
                        courtMethod.value = 'PUT';
                        courtTitle.textContent = 'Uredi igrišče';
                        courtSubmit.textContent = 'Shrani spremembe';
                        courtCancel.style.display = 'inline-block';
                        courtNumberInput.value = btn.dataset.number;
                        courtImageInput.value = btn.dataset.imagePath;
                        courtForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                });

                courtCancel.addEventListener('click', resetCourtForm);

                // Reservation form: available-slots lookup plus "add" / "edit" mode.
                const reservationForm = document.getElementById('reservation-form');
                const resMethod = document.getElementById('res-method');
                const resTitle = document.getElementById('res-form-title');
                const resCancel = document.getElementById('res-cancel');
                const reservationStoreUrl = reservationForm.action;

                const courtSelect = document.getElementById('res-court');
                const dateInput = document.getElementById('res-date');
                const durationSelect = document.getElementById('res-duration');
                const slotsContainer = document.getElementById('res-slots');
                const slotsHint = document.getElementById('res-slots-hint');
                const startingHourInput = document.getElementById('res-starting-hour');
                const submitButton = document.getElementById('res-submit');
                const nameInput = document.getElementById('res-name');
                const surnameInput = document.getElementById('res-surname');
                const emailInput = document.getElementById('res-email');

                let editingReservationId = null;
                let pendingSelectHour = null;

                function resetSlots(message) {
                    slotsContainer.innerHTML = '';
                    startingHourInput.value = '';
                    submitButton.disabled = true;
                    slotsHint.textContent = message;
                    slotsHint.style.display = 'block';
                }

                function resetReservationForm() {
                    reservationForm.reset();
                    reservationForm.action = reservationStoreUrl;
                    resMethod.value = 'POST';
                    resTitle.textContent = 'Dodaj rezervacijo';
                    submitButton.textContent = 'Dodaj rezervacijo';
                    resCancel.style.display = 'none';
                    editingReservationId = null;
                    pendingSelectHour = null;
                    resetSlots('Izberi igrišče in datum, da se prikažejo prosti termini.');
                }

                function loadSlots() {
                    const court = courtSelect.value;
                    const date = dateInput.value;
                    const duration = durationSelect.value;

                    if (! court || ! date || ! duration) {
                        resetSlots('Izberi igrišče in datum, da se prikažejo prosti termini.');
                        return;
                    }

                    const autoSelectHour = pendingSelectHour;
                    pendingSelectHour = null;

                    resetSlots('Nalaganje ...');

                    const params = new URLSearchParams({ court, date, duration });

                    if (editingReservationId) {
                        params.set('exclude', editingReservationId);
                    }

                    fetch('{{ route('reservations.available-slots') }}?' + params.toString())
                        .then((response) => response.json())
                        .then((data) => {
                            slotsContainer.innerHTML = '';

                            if (! data.slots || data.slots.length === 0) {
                                slotsHint.textContent = 'Ni prostih terminov za izbrani datum.';
                                slotsHint.style.display = 'block';
                                return;
                            }

                            slotsHint.style.display = 'none';

                            data.slots.forEach((slot) => {
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.className = 'slot-btn';
                                button.textContent = slot.startingHour + ' - ' + slot.endingHour;
                                button.dataset.startingHour = slot.startingHour;

                                button.addEventListener('click', () => {
                                    document.querySelectorAll('.slot-btn').forEach((btn) => btn.classList.remove('selected'));
                                    button.classList.add('selected');
                                    startingHourInput.value = parseInt(slot.startingHour.split(':')[0], 10);
                                    submitButton.disabled = false;
                                });

                                slotsContainer.appendChild(button);

                                if (autoSelectHour && slot.startingHour === autoSelectHour) {
                                    button.click();
                                }
                            });
                        })
                        .catch(() => {
                            slotsHint.textContent = 'Napaka pri nalaganju terminov.';
                            slotsHint.style.display = 'block';
                        });
                }

                courtSelect.addEventListener('change', loadSlots);
                dateInput.addEventListener('change', loadSlots);
                durationSelect.addEventListener('change', loadSlots);

                document.querySelectorAll('.reservation-edit-btn').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        reservationForm.action = btn.dataset.updateUrl;
                        resMethod.value = 'PUT';
                        resTitle.textContent = 'Uredi rezervacijo';
                        submitButton.textContent = 'Shrani spremembe';
                        resCancel.style.display = 'inline-block';

                        editingReservationId = btn.dataset.id;
                        pendingSelectHour = btn.dataset.startingHour;

                        courtSelect.value = btn.dataset.court;
                        dateInput.value = btn.dataset.date;
                        durationSelect.value = btn.dataset.duration;
                        nameInput.value = btn.dataset.name;
                        surnameInput.value = btn.dataset.surname;
                        emailInput.value = btn.dataset.email;

                        loadSlots();

                        reservationForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                });

                resCancel.addEventListener('click', resetReservationForm);
            })();
        </script>
    </body>
</html>
