const express = require("express");
const router = express.Router();
const { pridobiMozneRezervacije } = require("../utils/courtReservations");
const Court = require("../models/Court");

router.get("/", async (req, res) => {
  const courts = await Court.find().populate("reservations");
  res.json(courts);
});

router.get("/:id", async (req, res) => {
  const courts = await Court.findById(req.params.id).populate("reservations");
  res.json(courts);
});

router.get("/:id/rezervacije", async (req, res) => {
  const datum = req.query.date;
  const court = await Court.findById(req.params.id).populate("reservations");
  const rezervacije = pridobiMozneRezervacije(court.reservations, datum);
  res.json(rezervacije);
});

router.post("/", async (req, res) => {
  const court = await Court.create(req.body);
  res.status(201).json(court);
});

router.put("/:id", async (req,res) =>{
  const court = await Court.findByIdAndUpdate(req.params.id, req.body)
  res.json(court);
});

router.delete("/:id", async (req, res) => {
  const court = await Court.findByIdAndDelete(req.params.id);
  res.json(court);
});
module.exports = router;