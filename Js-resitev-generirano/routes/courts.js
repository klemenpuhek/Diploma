const express = require("express");
const router = express.Router();
const Court = require("../models/Court");
const { getAvailableOptions } = require("../utils/availability");
const { requireAuthApi } = require("../middleware/requireAuth");

router.get("/", async (req, res) => {
  const courts = await Court.find().populate("reservations");
  res.json(courts);
});

router.get("/:id", async (req, res) => {
  const court = await Court.findById(req.params.id).populate("reservations");
  if (!court) return res.status(404).json({ error: "Court not found" });
  res.json(court);
});

router.get("/:id/availability", async (req, res) => {
  const { date } = req.query;
  if (!date) return res.status(400).json({ error: "date query param is required" });

  const court = await Court.findById(req.params.id).populate("reservations");
  if (!court) return res.status(404).json({ error: "Court not found" });

  const options = getAvailableOptions(court.reservations, date);
  res.json(options);
});

router.post("/", requireAuthApi, async (req, res) => {
  const court = await Court.create(req.body);
  res.status(201).json(court);
});

router.put("/:id", requireAuthApi, async (req, res) => {
  const court = await Court.findByIdAndUpdate(req.params.id, req.body, {
    new: true,
    runValidators: true,
  });
  if (!court) return res.status(404).json({ error: "Court not found" });
  res.json(court);
});

router.delete("/:id", requireAuthApi, async (req, res) => {
  const court = await Court.findByIdAndDelete(req.params.id);
  if (!court) return res.status(404).json({ error: "Court not found" });
  res.status(204).end();
});

module.exports = router;
