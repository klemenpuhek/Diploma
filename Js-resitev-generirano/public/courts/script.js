const API = "/api";

const courtsGrid = document.getElementById("courts-grid");
const modalOverlay = document.getElementById("modal-overlay");
const modalClose = document.getElementById("modal-close");
const modalTitle = document.getElementById("modal-title");
const modalCourtIdInput = document.getElementById("modal-court-id");
const modalNameInput = document.getElementById("modal-name");
const modalSurnameInput = document.getElementById("modal-surname");
const modalEmailInput = document.getElementById("modal-email");
const modalDateInput = document.getElementById("modal-date");
const modalDurationSelect = document.getElementById("modal-duration");
const modalStartSelect = document.getElementById("modal-start");
const modalMessage = document.getElementById("modal-message");
const reservationForm = document.getElementById("reservation-form");

async function loadCourts() {
  const res = await fetch(`${API}/courts`);
  const courts = await res.json();

  courtsGrid.innerHTML = "";
  courts.forEach((court) => {
    const card = document.createElement("button");
    card.type = "button";
    card.className = "court-card";
    card.style.backgroundImage = court.imagePath ? `url(${court.imagePath})` : "none";
    card.setAttribute("aria-label", `Igrišče ${court.number}`);
    card.addEventListener("click", () => openModal(court));
    courtsGrid.appendChild(card);
  });
}

function showMessage(text, type) {
  modalMessage.textContent = text;
  modalMessage.className = `message ${type}`;
}

function openModal(court) {
  modalCourtIdInput.value = court._id;
  modalTitle.textContent = `Rezervacija - Igrišče ${court.number}`;
  reservationForm.reset();
  modalCourtIdInput.value = court._id;
  modalStartSelect.innerHTML = '<option value="">Najprej izberi datum</option>';
  modalMessage.className = "message hidden";
  modalOverlay.classList.remove("hidden");
}

function closeModal() {
  modalOverlay.classList.add("hidden");
}

modalClose.addEventListener("click", closeModal);
modalOverlay.addEventListener("click", (e) => {
  if (e.target === modalOverlay) closeModal();
});

function hourFromRangeLabel(label) {
  return label.split(" - ")[0];
}

async function refreshStartOptions() {
  const courtId = modalCourtIdInput.value;
  const date = modalDateInput.value;
  const duration = modalDurationSelect.value;

  if (!courtId || !date) {
    modalStartSelect.innerHTML = '<option value="">Najprej izberi datum</option>';
    return;
  }

  const res = await fetch(`${API}/courts/${courtId}/availability?date=${date}`);
  const options = await res.json();
  const list = duration === "2" ? options.twoHourOptions : options.oneHourOptions;

  if (!list.length) {
    modalStartSelect.innerHTML = '<option value="">Ni prostih terminov</option>';
    return;
  }

  modalStartSelect.innerHTML = list
    .map((label) => `<option value="${hourFromRangeLabel(label)}">${label}</option>`)
    .join("");
}

modalDateInput.addEventListener("change", refreshStartOptions);
modalDurationSelect.addEventListener("change", refreshStartOptions);

reservationForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  if (!modalStartSelect.value) {
    showMessage("Izberi prost termin.", "error");
    return;
  }

  const duration = Number(modalDurationSelect.value);
  const startHour = Number(modalStartSelect.value.split(":")[0]);
  const endingHour = `${String(startHour + duration).padStart(2, "0")}:00`;

  const payload = {
    court: modalCourtIdInput.value,
    name: modalNameInput.value,
    surname: modalSurnameInput.value,
    email: modalEmailInput.value,
    date: modalDateInput.value,
    startingHour: modalStartSelect.value,
    endingHour,
  };

  const res = await fetch(`${API}/reservations`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });

  if (res.ok) {
    showMessage("Rezervacija uspešna!", "success");
    setTimeout(closeModal, 1200);
  } else {
    showMessage("Rezervacija ni uspela. Poskusi znova.", "error");
  }
});

loadCourts();
