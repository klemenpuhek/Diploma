const OPENING_HOUR = 8;
const CLOSING_HOUR = 22;

function parseHour(value) {
  return parseInt(String(value).split(":")[0], 10);
}

function formatHour(hour) {
  return `${String(hour).padStart(2, "0")}:00`;
}

function isSameDate(a, b) {
  return new Date(a).toDateString() === new Date(b).toDateString();
}

function overlaps(startA, endA, startB, endB) {
  return startA < endB && startB < endA;
}

function getAvailableOptions(reservations, date, now = new Date()) {
  const dayReservations = reservations.filter((r) => isSameDate(r.date, date));

  const isFree = (start, end) =>
    !dayReservations.some((r) =>
      overlaps(start, end, parseHour(r.startingHour), parseHour(r.endingHour))
    );

  const isPast = (start) => isSameDate(date, now) && start <= now.getHours();

  const oneHourOptions = [];
  for (let start = OPENING_HOUR; start < CLOSING_HOUR; start++) {
    const end = start + 1;
    if (!isPast(start) && isFree(start, end)) {
      oneHourOptions.push(`${formatHour(start)} - ${formatHour(end)}`);
    }
  }

  const twoHourOptions = [];
  for (let start = OPENING_HOUR; start <= CLOSING_HOUR - 2; start++) {
    const end = start + 2;
    if (!isPast(start) && isFree(start, end)) {
      twoHourOptions.push(`${formatHour(start)} - ${formatHour(end)}`);
    }
  }

  return { oneHourOptions, twoHourOptions };
}

module.exports = { getAvailableOptions };
