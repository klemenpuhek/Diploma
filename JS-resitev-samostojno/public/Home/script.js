const courtsGrid = document.getElementById("courtsGrid");
const modalOverlay = document.getElementById("modalOverlay");
const modalClose = document.getElementById("modalClose");
const modalTitle = document.getElementById("modalTitle");
const reservationForm = document.getElementById("reservationForm");
const modalCourtId = document.getElementById("modalCourtId");
const modalName = document.getElementById("modalName");
const modalSurname = document.getElementById("modalSurname");
const modalEmail = document.getElementById("modalEmail");
const modalDate = document.getElementById("modalDate");
const modalDuration = document.getElementById("modalDuration");
const modalStart = document.getElementById("modalStart");

async function naloziIgrisca() {
    const response = await fetch("/api/courts");
    const igrisca = await response.json();

    igrisca.forEach((i) => {
        const button = document.createElement("button");
        button.classList.add("courtCard");
        button.style.backgroundImage = `url(/images/${i.imagePath})`;

        button.addEventListener("click", (e) => {
            odpriModalnoOkno(i);
        });

        courtsGrid.append(button);
        console.log("dodana igrsica");
    });
}
naloziIgrisca();

async function odpriModalnoOkno(igrisce) {
    modalCourtId.value = igrisce._id;
    modalOverlay.classList.remove("hidden");
}

async function PrikaziOpcije() {
    const igrisce = modalCourtId.value;
    const response = await fetch(
        `/api/courts/${igrisce}/rezervacije?date=${modalDate.value}`,
    );
    const podatki = await response.json();

    let list;
    if (modalDuration.value === "1") {
        list = podatki.mapiraneEnoUrne;
    } else {
        list = podatki.mapiraneDvoUrne;
    }

    modalStart.innerHTML = list
        .map((label) => {
            const [start, end] = label;
            const besedilo = `${String(start).padStart(2, "0")}:00 - ${String(end).padStart(2, "0")}:00`;
            return `<option value="${start}">${besedilo}</option>`;
        })
        .join("");
}

modalDate.addEventListener("change", PrikaziOpcije);
modalDuration.addEventListener("change", PrikaziOpcije);

modalClose.addEventListener("click", (e) => {
    modalOverlay.classList.add("hidden");
});

reservationForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const duration = Number(modalDuration.value);
    const startHour = Number(modalStart.value.split(":")[0]);
    const endingHour = `${String(startHour + duration).padStart(2, "0")}:00`;

    const podatki = {
        court: modalCourtId.value,
        name: modalName.value,
        surname: modalSurname.value,
        email: modalEmail.value,
        date: modalDate.value,
        startingHour: startHour,
        endingHour: endingHour,
    };

    const res = await fetch(`/api/reservations`, {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(podatki),
    });
    reservationForm.reset();
});
