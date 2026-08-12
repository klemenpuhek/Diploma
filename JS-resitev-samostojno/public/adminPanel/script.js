const inputStIgrisca = document.getElementById("stIgrisca");
const inputPotDoSlike = document.getElementById("PotDoSlike");
const IdIgrisca = document.getElementById("IdIgrisca");
const buttonDodajIgrisce = document.getElementById("IgrisceSubmit");
const courtForm = document.getElementById("courtForm");
const courtsTableBody = document.getElementById("courtsTableBody");
const logoutButton = document.getElementById("LogoutButton")

async function naloziIgrisca() {
    const response = await fetch("/api/courts");
    const igrisca = await response.json();

    courtsTableBody.innerHTML = "";

    igrisca.forEach((igrisce) => {
        const tr = document.createElement("tr");

        const numberTd = document.createElement("td");
        numberTd.textContent = igrisce.number;

        const imagePathTd = document.createElement("td");
        imagePathTd.textContent = igrisce.imagePath || "-";

        const reservationsTd = document.createElement("td");
        reservationsTd.textContent = (igrisce.reservations || []).length;

        const actionsTd = document.createElement("td");

        const actionsDiv = document.createElement("div");

        const editButton = document.createElement("button");
        editButton.classList.add("editIgrisceGumb");
        editButton.dataset.id = igrisce._id;
        editButton.textContent = "Uredi";

        const deleteButton = document.createElement("button");
        deleteButton.classList.add("IzbrisiIgrisceGumb");
        deleteButton.dataset.id = igrisce._id;
        deleteButton.textContent = "Izbrisi";

        actionsDiv.appendChild(editButton);
        actionsDiv.appendChild(deleteButton);

        actionsTd.appendChild(actionsDiv);

        tr.appendChild(numberTd);
        tr.appendChild(imagePathTd);
        tr.appendChild(reservationsTd);
        tr.appendChild(actionsTd);

        courtsTableBody.appendChild(tr);
    });
    //console.log("dodano vse");
    PridobiIgrisca();
}

courtForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const podatki = {
        number: Number(inputStIgrisca.value),
        imagePath: inputPotDoSlike.value,
    };

    if (!IdIgrisca.value) {
        const response = await fetch("/api/courts", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(podatki),
        });
    } else {
        await fetch(`/api/courts/${IdIgrisca.value}`, {
            method: "PUT",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(podatki),
        });
    }

    courtForm.reset();
    naloziIgrisca();
    PridobiFilterIgrisca();
});

courtsTableBody.addEventListener("click", async (e) => {
    const idIgrisca = e.target.dataset.id;

    if (e.target.classList.contains("IzbrisiIgrisceGumb")) {
        await fetch(`/api/courts/${idIgrisca}`, {method: "DELETE"});
        naloziIgrisca();
        PridobiFilterIgrisca();
    }
    if (e.target.classList.contains("editIgrisceGumb")) {
        const response = await fetch(`/api/courts/${idIgrisca}`);
        const igrisce = await response.json();

        IdIgrisca.value = igrisce._id;
        inputStIgrisca.value = igrisce.number;
        inputPotDoSlike.value = igrisce.imagePath;
    }
});

naloziIgrisca();

// rezervacije
const InputIgrisceRezervacija = document.getElementById("IgrisceRezervacija");
const InputImeRezervacije = document.getElementById("ImeRezervacija");
const InputPriimekRezervacije = document.getElementById("PriimekRezervacija");
const InputEmailRezervacije = document.getElementById("EmailRezervacija");
const InputDatumRezervacije = document.getElementById("DatumRezervacija");
const InputTrajanjeRezervacije = document.getElementById("TrajanjeRezervacija");
const InputZacetekRezervacije = document.getElementById("ZacetekRezervacija");
const ButtonDodajRezervacije = document.getElementById("RezervacijaSubmit");
const IdRezervacije = document.getElementById("IdRezervacije");

const reservationForm = document.getElementById("reservationForm");
const reservationsTableBody = document.getElementById("reservationsTableBody");
const filterIgrisca = document.getElementById("filterIgrisca");
const filterSearch = document.getElementById("filterSearch");

async function PridobiIgrisca() {
    const response = await fetch(`/api/courts`);
    const podatki = await response.json();

    InputIgrisceRezervacija.innerHTML = podatki
        .map((c) => `<option value="${c._id}">Igrišče ${c.number}</option>`)
        .join("");
}

PridobiIgrisca();

async function PrikaziRezervacije() {
    const response = await fetch(`/api/reservations`);
    const reservations = await response.json();

    const izbranoIgrisce = filterIgrisca.value;
    const iskanje = filterSearch.value.trim().toLowerCase();

    const filtered = [];

    for (let i = 0; i < reservations.length; i++) {
        const r = reservations[i];

        let ustrezaIgriscu;
        if (izbranoIgrisce === "") {
            ustrezaIgriscu = true;
        } else {
            ustrezaIgriscu = r.court._id === izbranoIgrisce;
        }

        let ustrezaIskanju;
        if (iskanje === "") {
            ustrezaIskanju = true;
        } else {
            const imeMala = r.name.toLowerCase();
            const priimekMala = r.surname.toLowerCase();
            ustrezaIskanju =
                imeMala.includes(iskanje) || priimekMala.includes(iskanje);
        }

        if (ustrezaIgriscu && ustrezaIskanju) {
            filtered.push(r);
        }
    }

    reservationsTableBody.innerHTML = "";

    filtered.forEach((r) => {
        const tr = document.createElement("tr");

        const courtTd = document.createElement("td");
        courtTd.textContent = r.court ? `Igrišče ${r.court.number}` : "-";

        const nameTd = document.createElement("td");
        nameTd.textContent = r.name;

        const surnameTd = document.createElement("td");
        surnameTd.textContent = r.surname;

        const emailTd = document.createElement("td");
        emailTd.textContent = r.email;

        const dateTd = document.createElement("td");
        dateTd.textContent = new Date(r.date).toLocaleDateString("sl-SI");

        const hourTd = document.createElement("td");
        hourTd.textContent = `${r.startingHour} - ${r.endingHour}`;

        const actionsTd = document.createElement("td");
        const actionsDiv = document.createElement("div");

        const editButton = document.createElement("button");
        editButton.classList.add("UrediRezervacijo");
        editButton.dataset.id = r._id;
        editButton.textContent = "Uredi";

        const deleteButton = document.createElement("button");
        deleteButton.classList.add("IzbrisiRezervacijo");
        deleteButton.dataset.id = r._id;
        deleteButton.textContent = "Izbriši";

        actionsDiv.appendChild(editButton);
        actionsDiv.appendChild(deleteButton);
        actionsTd.appendChild(actionsDiv);

        tr.appendChild(courtTd);
        tr.appendChild(nameTd);
        tr.appendChild(surnameTd);
        tr.appendChild(emailTd);
        tr.appendChild(dateTd);
        tr.appendChild(hourTd);
        tr.appendChild(actionsTd);

        reservationsTableBody.appendChild(tr);
    });
}

PrikaziRezervacije();

async function PrikaziOpcije() {
    const igrisce = InputIgrisceRezervacija.value;
    const response = await fetch(
        `/api/courts/${igrisce}/rezervacije?date=${InputDatumRezervacije.value}`,
    );
    const podatki = await response.json();

    let list;
    if (InputTrajanjeRezervacije.value === "1") {
        list = podatki.mapiraneEnoUrne;
    } else {
        list = podatki.mapiraneDvoUrne;
    }

    InputZacetekRezervacije.innerHTML = list
        .map((label) => {
            const [start, end] = label;
            const besedilo = `${String(start).padStart(2, "0")}:00 - ${String(end).padStart(2, "0")}:00`;
            return `<option value="${start}">${besedilo}</option>`;
        })
        .join("");
}

InputIgrisceRezervacija.addEventListener("change", PrikaziOpcije);
InputDatumRezervacije.addEventListener("change", PrikaziOpcije);
InputTrajanjeRezervacije.addEventListener("change", PrikaziOpcije);

reservationForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const start = Number(InputZacetekRezervacije.value);
    const trajanje = Number(InputTrajanjeRezervacije.value);

    const podatki = {
        court: InputIgrisceRezervacija.value,
        name: InputImeRezervacije.value,
        surname: InputPriimekRezervacije.value,
        email: InputEmailRezervacije.value,
        date: InputDatumRezervacije.value,
        startingHour: `${String(start).padStart(2, "0")}:00`,
        endingHour: `${String(start + trajanje).padStart(2, "0")}:00`,
    };

    if (!IdRezervacije.value) {
        await fetch("/api/reservations", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(podatki),
        });
    } else {
        await fetch(`/api/reservations/${IdRezervacije.value}`, {
            method: "PUT",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(podatki),
        });
    }

    reservationForm.reset();
    PrikaziRezervacije();
});

reservationsTableBody.addEventListener("click", async (e) => {
    const idRezervacije = e.target.dataset.id;

    if (e.target.classList.contains("IzbrisiRezervacijo")) {
        await fetch(`/api/reservations/${idRezervacije}`, {method: "DELETE"});
        PrikaziRezervacije();
    }

    if (e.target.classList.contains("UrediRezervacijo")) {
        const response = await fetch(`/api/reservations/${idRezervacije}`);
        const r = await response.json();

        IdRezervacije.value = r._id;
        InputIgrisceRezervacija.value = r.court._id;
        InputImeRezervacije.value = r.name;
        InputPriimekRezervacije.value = r.surname;
        InputEmailRezervacije.value = r.email;
        InputDatumRezervacije.value = r.date.slice(0, 10);
    }
});

async function PridobiFilterIgrisca() {
    const response = await fetch(`/api/courts`);
    const podatki = await response.json();

    filterIgrisca.innerHTML =
        `<option value="">Vsa igrišča</option>` +
        podatki
            .map((c) => `<option value="${c._id}">Igrišče ${c.number}</option>`)
            .join("");
}

PridobiFilterIgrisca();

filterIgrisca.addEventListener("change", PrikaziRezervacije);
filterSearch.addEventListener("input", PrikaziRezervacije);

logoutButton.addEventListener("click", async (e) => {
    e.preventDefault();

    const res = await fetch("/api/auth/logout", {
        method: "POST",
    });

    if (res.ok) {
        window.location.href = "/adminLogin";
    }
});

