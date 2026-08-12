<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervacija igrišč</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <div class="mainView">
        <nav class="sidePanel">
            <div class="sidePanelLinks">
                <a href="{{ route('loadDataHome') }}" class="navLink">Rezervacije</a>
            </div>
        </nav>

        <div class="content">
            <header class="topbar">
                <h1>Rezervacija</h1>
            </header>

            <section class="courtsGrid" id="courtsGrid">
                @foreach ($courts as $court)
                    <button
                        type="button"
                        class="courtCard"
                        data-id="{{ $court->id }}"
                        data-number="{{ $court->number }}"
                        style="background-image: url('{{ asset($court->imagePath) }}')"
                    ></button>
                @endforeach
            </section>

            <div class="modalOverlay hidden" id="modalOverlay">
                <div class="modal">
                    <button type="button" class="modalClose" id="modalClose">&times;</button>
                    <h2 id="modalTitle">Rezervacija</h2>

                    <form id="reservationForm" action="{{ route('addReservationHome') }}" method="POST">
                        @csrf
                        <input type="hidden" id="modalCourtId" name="court">
                        <input type="text" id="modalName" name="name" placeholder="Ime" required>
                        <input type="text" id="modalSurname" name="surname" placeholder="Priimek" required>
                        <input type="email" id="modalEmail" name="email" placeholder="Email" required>
                        <input type="date" id="modalDate" name="date" required>
                        <select id="modalDuration" name="duration" required>
                            <option value="1">1 ura</option>
                            <option value="2">2 uri</option>
                        </select>
                        <select id="modalStart" name="startingHour" required>
                            <option value="">Najprej izberi datum</option>
                        </select>
                        <div class="formActions">
                            <button type="submit">Rezerviraj</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>