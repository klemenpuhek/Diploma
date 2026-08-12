const express = require("express");
const router = express.Router();
const Reservation = require("../models/Reservation");
const Court = require("../models/Court");
const { requireAuthApi } = require("../middleware/requireAuth");

router.get("/", requireAuthApi, async (req, res) => {
  const reservations = await Reservation.find().populate("court");
  res.json(reservations);
});

router.get("/:id", requireAuthApi, async (req, res) => {
  const reservation = await Reservation.findById(req.params.id).populate("court");
  if (!reservation) return res.status(404).json({ error: "Reservation not found" });
  res.json(reservation);
});

router.post("/", async (req, res) => {
  const court = await Court.findById(req.body.court);
  if (!court) return res.status(400).json({ error: "Court not found" });

  const { name, surname, email, date, startingHour, endingHour } = req.body;
  const reservation = await Reservation.create({
    court: court._id,
    name,
    surname,
    email,
    date,
    startingHour,
    endingHour,
  });
  court.reservations.push(reservation._id);
  await court.save();

  res.status(201).json(reservation);
});

router.put("/:id", requireAuthApi, async (req, res) => {
  const { court, ...rest } = req.body;
  const reservation = await Reservation.findByIdAndUpdate(req.params.id, rest, {
    new: true,
    runValidators: true,
  });
  if (!reservation) return res.status(404).json({ error: "Reservation not found" });
  res.json(reservation);
});

router.delete("/:id", requireAuthApi, async (req, res) => {
  const reservation = await Reservation.findByIdAndDelete(req.params.id);
  if (!reservation) return res.status(404).json({ error: "Reservation not found" });

  await Court.findByIdAndUpdate(reservation.court, { $pull: { reservations: reservation._id } });

  res.status(204).end();
});

module.exports = router;
