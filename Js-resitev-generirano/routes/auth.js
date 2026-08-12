const express = require("express");
const router = express.Router();
const admin = require("../models/Admin");

router.post("/login", (req, res) => {
  const { username, password } = req.body;

  if (username === admin.username && password === admin.password) {
    req.session.isAdmin = true;
    res.json({ message: "Login successful" });
  } else {
    res.status(401).json({ message: "Invalid username or password" });
  }
});

router.post("/logout", (req, res) => {
  req.session.destroy(() => {
    res.json({ message: "Logged out" });
  });
});

module.exports = router;
