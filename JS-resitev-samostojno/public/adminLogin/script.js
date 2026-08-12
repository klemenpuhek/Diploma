const authLogin = document.getElementById("authLogin");
const authName = document.getElementById("Name");
const authPassword = document.getElementById("Password");

authLogin.addEventListener("submit", async (e) => {
    e.preventDefault();

    const res = await fetch("/api/auth/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            username: authName.value,
            password: authPassword.value,
        }),
    });

    const data = await res.json();
    if (data.success) {
        window.location.href = "/adminPanel";
    } else {
        alert(data.message);
    }
});