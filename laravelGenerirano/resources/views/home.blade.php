<!DOCTYPE html>
<html lang="sl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rezervacije igrišč</title>
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    </head>
    <body>
        <div class="home-layout">
            <aside class="sidebar">
                <h1>Meni</h1>
                <button type="button" class="sidebar-tab active" data-tab="reservations">Rezervacije</button>
            </aside>

            <main class="home-content">
                <section id="tab-reservations">
                    <h2>Izberi igrišče</h2>

                    @if (session('success'))
                        <div class="admin-success">{{ session('success') }}</div>
                    @endif

                    <div class="court-grid">
                        @forelse ($courts as $court)
                            <button
                                type="button"
                                class="court-card"
                                style="background-image: url('{{ $court->imagePath }}')"
                                data-id="{{ $court->id }}"
                                data-number="{{ $court->number }}"
                            >
                                <span>Igrišče {{ $court->number }}</span>
                            </button>
                        @empty
                            <p>Trenutno ni na voljo nobenega igrišča.</p>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>

        <div class="modal-overlay" id="reservation-modal" style="display:none">
            <div class="modal-box">
                <button type="button" class="modal-close" id="modal-close">&times;</button>
                <h2 id="modal-title">Rezervacija</h2>

                <div class="admin-error" id="modal-error" style="display:none"></div>

                <form method="POST" action="{{ route('reservations.store') }}" id="reservation-form">
                    @csrf
                    <input type="hidden" name="court" id="pr-court">

                    <div class="admin-field">
                        <label for="pr-date">Datum</label>
                        <input id="pr-date" type="date" name="date" required>
                    </div>

                    <div class="admin-field">
                        <label for="pr-duration">Trajanje</label>
                        <select id="pr-duration" name="duration" required>
                            <option value="1">1 ura</option>
                            <option value="2">2 uri</option>
                        </select>
                    </div>

                    <div class="admin-field">
                        <label>Prosti termini</label>
                        <div id="pr-slots" class="slots"></div>
                        <div id="pr-slots-hint" class="slots-hint">Izberi datum, da se prikažejo prosti termini.</div>
                        <input type="hidden" name="startingHour" id="pr-starting-hour" required>
                    </div>

                    <div class="admin-field">
                        <label for="pr-name">Ime</label>
                        <input id="pr-name" type="text" name="name" required>
                    </div>

                    <div class="admin-field">
                        <label for="pr-surname">Priimek</label>
                        <input id="pr-surname" type="text" name="surname" required>
                    </div>

                    <div class="admin-field">
                        <label for="pr-email">Email</label>
                        <input id="pr-email" type="email" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-block" id="pr-submit" disabled>Rezerviraj</button>
                </form>
            </div>
        </div>

        <script>
            (function () {
                const courtsById = @json($courts->mapWithKeys(fn ($court) => [$court->id => $court->number]));

                const modal = document.getElementById('reservation-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalError = document.getElementById('modal-error');
                const modalClose = document.getElementById('modal-close');

                const form = document.getElementById('reservation-form');
                const courtInput = document.getElementById('pr-court');
                const dateInput = document.getElementById('pr-date');
                const durationSelect = document.getElementById('pr-duration');
                const slotsContainer = document.getElementById('pr-slots');
                const slotsHint = document.getElementById('pr-slots-hint');
                const startingHourInput = document.getElementById('pr-starting-hour');
                const submitButton = document.getElementById('pr-submit');
                const nameInput = document.getElementById('pr-name');
                const surnameInput = document.getElementById('pr-surname');
                const emailInput = document.getElementById('pr-email');

                function resetSlots(message) {
                    slotsContainer.innerHTML = '';
                    startingHourInput.value = '';
                    submitButton.disabled = true;
                    slotsHint.textContent = message;
                    slotsHint.style.display = 'block';
                }

                function loadSlots() {
                    const court = courtInput.value;
                    const date = dateInput.value;
                    const duration = durationSelect.value;

                    if (! court || ! date || ! duration) {
                        resetSlots('Izberi datum, da se prikažejo prosti termini.');
                        return;
                    }

                    resetSlots('Nalaganje ...');

                    const params = new URLSearchParams({ court, date, duration });

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
                            });
                        })
                        .catch(() => {
                            slotsHint.textContent = 'Napaka pri nalaganju terminov.';
                            slotsHint.style.display = 'block';
                        });
                }

                function openModal(courtId) {
                    form.reset();
                    modalError.style.display = 'none';
                    courtInput.value = courtId;
                    modalTitle.textContent = 'Rezervacija - Igrišče ' + courtsById[courtId];
                    resetSlots('Izberi datum, da se prikažejo prosti termini.');
                    modal.style.display = 'flex';
                }

                function closeModal() {
                    modal.style.display = 'none';
                }

                document.querySelectorAll('.court-card').forEach((card) => {
                    card.addEventListener('click', () => openModal(card.dataset.id));
                });

                modalClose.addEventListener('click', closeModal);
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                dateInput.addEventListener('change', loadSlots);
                durationSelect.addEventListener('change', loadSlots);

                @if ($errors->any() && old('court'))
                    openModal(@json(old('court')));
                    dateInput.value = @json(old('date'));
                    durationSelect.value = @json(old('duration'));
                    nameInput.value = @json(old('name'));
                    surnameInput.value = @json(old('surname'));
                    emailInput.value = @json(old('email'));
                    modalError.textContent = @json($errors->first());
                    modalError.style.display = 'block';
                    loadSlots();
                @endif
            })();
        </script>
    </body>
</html>
