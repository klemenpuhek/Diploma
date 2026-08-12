const mongoose = require("mongoose");

const reservationSchema = new mongoose.Schema(
  {
    court: { type: mongoose.Schema.Types.ObjectId, ref: "Court", required: true },
    name: { type: String, required: true },
    surname: { type: String, required: true },
    email: { type: String, required: true },
    date: { type: Date, required: true },
    startingHour: { type: String, required: true },
    endingHour: { type: String, required: true },
  }
);

module.exports = mongoose.model("Reservation", reservationSchema);
