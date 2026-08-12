const express = require("express");
const router = express.Router();
const Admin = require("../models/Admin");

router.post("/login", (req, res) => {
    const { username, password } = req.body;

    if (username === Admin.username && password === Admin.password) {
        req.session.isAdmin = true;
        return res.json({ success: true });
    }

    res.status(401).json({ success: false, message: "Napačni podatki" });
});

router.post("/logout", (req, res) => {
    req.session.destroy(() => {
        res.json({ success: true });
    });
});

module.exports = router;