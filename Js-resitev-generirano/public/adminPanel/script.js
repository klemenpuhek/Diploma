const API = "/api";

let courtsCache = [];

// ---------- Courts ----------

const courtForm = document.getElementById("court-form");
const courtIdInput = document.getElementById("court-id");
const courtNumberInput = document.getElementById("court-number");
const courtImageInput = document.getElementById("court-image");
const courtSubmitBtn = document.getElementById("court-submit-btn");
const courtCancelBtn = document.getElementById("court-cancel-btn");
const courtsTableBody = document.getElementById("courts-table-body");
const reservationCourtSelect = document.getElementById("reservation-court");
const filterCourtSelect = document.getElementById("filter-court");
const filterSearchInput = document.getElementById("filter-search");

async function loadCourts() {
  const res = await fetch(`${API}/courts`);
  const courts = await res.json();
  courtsCache = courts;

  courtsTableBody.innerHTML = "";
  courts.forEach((court) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${court.number}</td>
      <td>${court.imagePath || "-"}</td>
      <td>${(court.reservations || []).length}</td>
      <td>
        <div class="row-actions">
          <button class="edit-btn" data-id="${court._id}">Uredi</button>
          <button class="delete-btn" data-id="${court._id}">Izbriši</button>
        </div>
      </td>
    `;
    courtsTableBody.appendChild(tr);
  });

  const currentValue = reservationCourtSelect.value;
  reservationCourtSelect.innerHTML = '<option value="">Izberi igrišče</option>';
  courts.forEach((court) => {
    reservationCourtSelect.innerHTML += `<option value="${court._id}">Igrišče ${court.number}</option>`;
  });
  reservationCourtSelect.value = currentValue;

  const currentFilterValue = filterCourtSelect.value;
  filterCourtSelect.innerHTML = '<option value="">Vsa igrišča</option>';
  courts.forEach((court) => {
    filterCourtSelect.innerHTML += `<option value="${court._id}">Igrišče ${court.number}</option>`;
  });
  filterCourtSelect.value = currentFilterValue;
}

function resetCourtForm() {
  courtIdInput.value = "";
  courtForm.reset();
  courtSubmitBtn.textContent = "Dodaj igrišče";
  courtCancelBtn.classList.add("hidden");
}

courtForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const id = courtIdInput.value;
  const payload = {
    number: Number(courtNumberInput.value),
    imagePath: courtImageInput.value,
  };

  if (id) {
    await fetch(`${API}/courts/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
  } else {
    await fetch(`${API}/courts`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
  }

  resetCourtForm();
  loadCourts();
});

courtCancelBtn.addEventListener("click", resetCourtForm);

courtsTableBody.addEventListener("click", async (e) => {
  const id = e.target.dataset.id;
  if (!id) return;

  if (e.target.classList.contains("delete-btn")) {
    if (!confirm("Izbrišem igrišče?")) return;
    await fetch(`${API}/courts/${id}`, { method: "DELETE" });
    loadCourts();
  }

  if (e.target.classList.contains("edit-btn")) {
    const res = await fetch(`${API}/courts/${id}`);
    const court = await res.json();
    courtIdInput.value = court._id;
    courtNumberInput.value = court.number;
    courtImageInput.value = court.imagePath || "";
    courtSubmitBtn.textContent = "Shrani spremembe";
    courtCancelBtn.classList.remove("hidden");
  }
});

// ---------- Reservations ----------

const reservationForm = document.getElementById("reservation-form");
const reservationIdInput = document.getElementById("reservation-id");
const reservationNameInput = document.getElementById("reservation-name");
const reservationSurnameInput = document.getElementById("reservation-surname");
const reservationEmailInput = document.getElementById("reservation-email");
const reservationDateInput = document.getElementById("reservation-date");
const reservationDurationSelect = document.getElementById("reservation-duration");
const reservationStartSelect = document.getElementById("reservation-start");
const reservationSubmitBtn = document.getElementById("reservation-submit-btn");
const reservationCancelBtn = document.getElementById("reservation-cancel-btn");
const reservationsTableBody = document.getElementById("reservations-table-body");

function hourFromRangeLabel(label) {
  return label.split(" - ")[0];
}

async function refreshStartOptions() {
  const courtId = reservationCourtSelect.value;
  const date = reservationDateInput.value;
  const duration = reservationDurationSelect.value;

  if (!courtId || !date) {
    reservationStartSelect.innerHTML = '<option value="">Najprej izberi igrišče in datum</option>';
    return;
  }

  const res = await fetch(`${API}/courts/${courtId}/availability?date=${date}`);
  const options = await res.json();
  const list = duration === "2" ? options.twoHourOptions : options.oneHourOptions;

  if (!list.length) {
    reservationStartSelect.innerHTML = '<option value="">Ni prostih terminov</option>';
    return;
  }

  reservationStartSelect.innerHTML = list
    .map((label) => `<option value="${hourFromRangeLabel(label)}">${label}</option>`)
    .join("");
}

reservationCourtSelect.addEventListener("change", refreshStartOptions);
reservationDateInput.addEventListener("change", refreshStartOptions);
reservationDurationSelect.addEventListener("change", refreshStartOptions);

let reservationsCache = [];

function renderReservations() {
  const courtId = filterCourtSelect.value;
  const search = filterSearchInput.value.trim().toLowerCase();

  const filtered = reservationsCache.filter((r) => {
    const matchesCourt = !courtId || r.court?._id === courtId;
    const matchesSearch =
      !search ||
      r.name.toLowerCase().includes(search) ||
      r.surname.toLowerCase().includes(search);
    return matchesCourt && matchesSearch;
  });

  reservationsTableBody.innerHTML = "";
  filtered.forEach((r) => {
    const tr = document.createElement("tr");
    const date = new Date(r.date).toLocaleDateString("sl-SI");
    const courtLabel = r.court ? `Igrišče ${r.court.number}` : "-";
    tr.innerHTML = `
      <td>${courtLabel}</td>
      <td>${r.name}</td>
      <td>${r.surname}</td>
      <td>${r.email}</td>
      <td>${date}</td>
      <td>${r.startingHour} - ${r.endingHour}</td>
      <td>
        <div class="row-actions">
          <button class="edit-btn" data-id="${r._id}">Uredi</button>
          <button class="delete-btn" data-id="${r._id}">Izbriši</button>
        </div>
      </td>
    `;
    reservationsTableBody.appendChild(tr);
  });
}

async function loadReservations() {
  const res = await fetch(`${API}/reservations`);
  reservationsCache = await res.json();
  renderReservations();
}

filterCourtSelect.addEventListener("change", renderReservations);
filterSearchInput.addEventListener("input", renderReservations);

function resetReservationForm() {
  reservationIdInput.value = "";
  reservationForm.reset();
  reservationStartSelect.innerHTML = '<option value="">Najprej izberi igrišče in datum</option>';
  reservationSubmitBtn.textContent = "Dodaj rezervacijo";
  reservationCancelBtn.classList.add("hidden");
}

reservationForm.addEventListener("submit", async (e) => {
  e.preventDefault();
  const id = reservationIdInput.value;
  const duration = Number(reservationDurationSelect.value);
  const startHour = Number(reservationStartSelect.value.split(":")[0]);
  const endingHour = `${String(startHour + duration).padStart(2, "0")}:00`;

  const payload = {
    court: reservationCourtSelect.value,
    name: reservationNameInput.value,
    surname: reservationSurnameInput.value,
    email: reservationEmailInput.value,
    date: reservationDateInput.value,
    startingHour: reservationStartSelect.value,
    endingHour,
  };

  if (id) {
    await fetch(`${API}/reservations/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
  } else {
    await fetch(`${API}/reservations`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
  }

  resetReservationForm();
  loadCourts();
  loadReservations();
});

reservationCancelBtn.addEventListener("click", resetReservationForm);

reservationsTableBody.addEventListener("click", async (e) => {
  const id = e.target.dataset.id;
  if (!id) return;

  if (e.target.classList.contains("delete-btn")) {
    if (!confirm("Izbrišem rezervacijo?")) return;
    await fetch(`${API}/reservations/${id}`, { method: "DELETE" });
    loadCourts();
    loadReservations();
  }

  if (e.target.classList.contains("edit-btn")) {
    const res = await fetch(`${API}/reservations/${id}`);
    const r = await res.json();
    reservationIdInput.value = r._id;
    reservationNameInput.value = r.name;
    reservationSurnameInput.value = r.surname;
    reservationEmailInput.value = r.email;
    reservationDateInput.value = new Date(r.date).toISOString().slice(0, 10);
    if (r.court) reservationCourtSelect.value = r.court._id;

    const durationHours =
      Number(r.endingHour.split(":")[0]) - Number(r.startingHour.split(":")[0]);
    reservationDurationSelect.value = String(durationHours);

    await refreshStartOptions();
    reservationStartSelect.innerHTML += `<option value="${r.startingHour}">${r.startingHour} - ${r.endingHour} (trenutna)</option>`;
    reservationStartSelect.value = r.startingHour;

    reservationSubmitBtn.textContent = "Shrani spremembe";
    reservationCancelBtn.classList.remove("hidden");
  }
});

// ---------- Logout ----------

document.getElementById("logout-btn").addEventListener("click", async () => {
  await fetch(`${API}/auth/logout`, { method: "POST" });
  window.location.href = "/adminLogin";
});

// ---------- Init ----------

loadCourts();
loadReservations();
