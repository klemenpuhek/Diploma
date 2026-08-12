const InputIgrisceRezervacija = document.getElementById("reservationCourt");
const InputDatumRezervacije = document.getElementById("reservationDate");
const InputTrajanjeRezervacije = document.getElementById("reservationDuration");
const InputZacetekRezervacije = document.getElementById("reservationStart");

async function PrikaziOpcije() {
    const igrisce = InputIgrisceRezervacija.value;
    const response = await fetch(
        `/reservation/available/${igrisce}?date=${InputDatumRezervacije.value}`,
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