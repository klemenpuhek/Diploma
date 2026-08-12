require("dotenv").config();
const path = require("path");
const express = require("express");
const session = require("express-session");
const connectDB = require("./config/db");
const { requireAuthPage } = require("./middleware/requireAuth");
const authRoutes = require("./routes/auth");
const reservationRoutes = require("./routes/reservations");
const courtRoutes = require("./routes/courts");

const app = express();
app.use(express.json());

app.use(
  session({
    secret: process.env.SESSION_SECRET || "badminton-secret",
    resave: false,
    saveUninitialized: false,
    cookie: { maxAge: 1000 * 60 * 60 * 8 },
  })
);

app.use("/api/auth", authRoutes);
app.use("/api/reservations", reservationRoutes);
app.use("/api/courts", courtRoutes);

app.use("/images", express.static(path.join(__dirname, "public/images")));
app.use("/shared", express.static(path.join(__dirname, "public/shared")));
app.use("/adminLogin", express.static(path.join(__dirname, "public/adminLogin")));
app.use("/adminPanel", requireAuthPage, express.static(path.join(__dirname, "public/adminPanel")));
app.use("/", express.static(path.join(__dirname, "public/courts")));

const PORT = process.env.PORT || 3000;

connectDB().then(() => {
  app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
});
