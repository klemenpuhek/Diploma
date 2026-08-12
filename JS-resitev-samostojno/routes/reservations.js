const express = require("express");
const router = express.Router();
const Reservation = require("../models/Reservation");
const Court = require("../models/Court");

function requireAdminApi(req, res, next) {
    if (req.session.isAdmin) {
        return next();
    }
    res.status(401).json({ success: false, message: "Ni prijave" });
}

router.get("/",requireAdminApi, async (req, res) => {
    const reservations = await Reservation.find().populate("court");
    res.json(reservations);
});

router.get("/:id",requireAdminApi, async (req, res) => {
    const reservation = await Reservation.findById(req.params.id).populate(
        "court",
    );
    res.json(reservation);
});

router.post("/", async (req, res) => {
    const court = await Court.findById(req.body.court);

    const {name, surname, email, date, startingHour, endingHour} = req.body;
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
    res.json(reservation);
});

router.put("/:id",requireAdminApi, async (req, res) => {
    const {name, surname, email, date, startingHour, endingHour} = req.body;
    const reservation = await Reservation.findByIdAndUpdate(
        req.params.id,
        {name, surname, email, date, startingHour, endingHour},
        {new: true},
    );
    res.json(reservation);
});

router.delete("/:id",requireAdminApi , async (req, res) => {
    const reservation = await Reservation.findByIdAndDelete(req.params.id);
    if (reservation) {
        await Court.findByIdAndUpdate(reservation.court, {
            $pull: {reservations: reservation._id},
        });
    }
    res.json(reservation);
});

module.exports = router;
