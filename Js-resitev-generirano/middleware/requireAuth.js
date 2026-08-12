function requireAuthPage(req, res, next) {
  if (req.session && req.session.isAdmin) {
    return next();
  }
  return res.redirect("/adminLogin");
}

function requireAuthApi(req, res, next) {
  if (req.session && req.session.isAdmin) {
    return next();
  }
  return res.status(401).json({ error: "Unauthorized" });
}

module.exports = { requireAuthPage, requireAuthApi };
