const mongoose = require("mongoose");

const courtSchema = new mongoose.Schema(
  {
    number: { type: Number, required: true },
    imagePath: { type: String },
    reservations: [{ type: mongoose.Schema.Types.ObjectId, ref: "Reservation" }],
  }
);

module.exports = mongoose.model("Court", courtSchema);
