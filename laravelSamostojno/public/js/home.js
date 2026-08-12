const courtsGrid = document.getElementById("courtsGrid");
const modalOverlay = document.getElementById("modalOverlay");
const modalClose = document.getElementById("modalClose");
const modalTitle = document.getElementById("modalTitle");
const modalCourtId = document.getElementById("modalCourtId");
const modalDate = document.getElementById("modalDate");
const modalDuration = document.getElementById("modalDuration");
const modalStart = document.getElementById("modalStart");

courtsGrid.querySelectorAll(".courtCard").forEach((card) => {
    card.addEventListener("click", () => {
        modalCourtId.value = card.dataset.id;
        modalTitle.textContent = `Rezervacija - Igrišče ${card.dataset.number}`;
        modalStart.innerHTML = '<option value="">Najprej izberi datum</option>';
        modalOverlay.classList.remove("hidden");
    });
});

modalClose.addEventListener("click", () => {
    modalOverlay.classList.add("hidden");
});

async function PrikaziOpcije() {
    const igrisce = modalCourtId.value;
    const datum = modalDate.value;

    if (!igrisce || !datum) return;

    const response = await fetch(`/reservation/available/${igrisce}?date=${datum}`);
    const podatki = await response.json();

    const list = modalDuration.value === "1"
        ? podatki.mapiraneEnoUrne
        : podatki.mapiraneDvoUrne;

    modalStart.innerHTML = list
        .map(([start, end]) => {
            const besedilo = `${String(start).padStart(2, "0")}:00 - ${String(end).padStart(2, "0")}:00`;
            return `<option value="${start}">${besedilo}</option>`;
        })
        .join("");
}

modalDate.addEventListener("change", PrikaziOpcije);
modalDuration.addEventListener("change", PrikaziOpcije);