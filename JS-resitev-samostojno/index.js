const express = require("express");
const session = require("express-session");
const path = require("path");
const Reservation = require("./models/Reservation");
const mongoose = require("mongoose");
const authRoutes = require("./routes/auth");
const courtRoutes = require("./routes/courts");
const reservationRoutes = require("./routes/reservations");

async function vzpostaviPovezavoDB() {
    await mongoose.connect("mongodb://localhost:27017/Diplomska-JS-generirano");
    console.log("povezava z bazo je vzpostavljena");
}
vzpostaviPovezavoDB();

const app = express();
app.use(express.json());
app.use(session({
    secret: "klemenklemenklemen",
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 1000 * 60 * 60 }
}));

function requireAdmin(req, res, next) {
    if (req.session.isAdmin) {
        return next();
    }
    res.redirect("/adminLogin");
}


app.use("/api/auth", authRoutes);
app.use("/api/courts", courtRoutes);
app.use("/api/reservations", reservationRoutes);


app.use("/adminPanel", requireAdmin, express.static(path.join(__dirname, "public/adminPanel")));
app.use("/adminLogin", express.static(path.join(__dirname, "public/adminLogin")));
app.use("/Home", express.static(path.join(__dirname,"public/Home")));

app.use("/images", express.static(path.join(__dirname, "public/images")));

app.get("/", (req, res) => {
  res.redirect("/Home");
});

app.listen(3000);


