function MapirajRezervacije(rezervacije) {
    const zasedeneUre = rezervacije.map((r) => [
        parseInt(r.startingHour),
        parseInt(r.endingHour),
    ]);

    return zasedeneUre;
}

function PridobiVseMapiraneFunkcije() {
    const mapiraneEnoUrne = [];

    for (let i = 8; i < 22; i++) {
        mapiraneEnoUrne.push([i, i + 1]);
    }

    const mapiraneDvoUrne = [];
    for (let i = 8; i < 21; i += 2) {
        mapiraneDvoUrne.push([i, i + 2]);
    }
    for (let i = 9; i < 21; i += 2) {
        mapiraneDvoUrne.push([i, i + 2]);
    }

    return {mapiraneEnoUrne, mapiraneDvoUrne};
}

function PridobiUgodneRezervacije(zasedeneRezervacije) {
    const { mapiraneEnoUrne, mapiraneDvoUrne } = PridobiVseMapiraneFunkcije();

    const jeProsto = ([start, end]) =>
        !zasedeneRezervacije.some((r) => {
            const zStart = parseInt(r.startingHour);
            const zEnd = parseInt(r.endingHour);
            return start < zEnd && zStart < end;
        });

    return {
        mapiraneEnoUrne: mapiraneEnoUrne.filter(jeProsto),
        mapiraneDvoUrne: mapiraneDvoUrne.filter(jeProsto),
    };
}

function pridobiMozneRezervacije(rezervacije, datum) {
    const rezervacijeNaIstiDan = [];

    rezervacije.forEach((rezervacija) => {
        if ( new Date(rezervacija.date).toDateString() === new Date(datum).toDateString()){
            rezervacijeNaIstiDan.push(rezervacija);
        }
    });

    return PridobiUgodneRezervacije(rezervacijeNaIstiDan);
}

module.exports = {pridobiMozneRezervacije};