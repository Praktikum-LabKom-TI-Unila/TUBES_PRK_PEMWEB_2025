document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".auth-form");
  if (!form) return;

  const isRegisterPage = document.getElementById("confirmPassword") !== null;

  document.querySelectorAll(".show-password").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.parentElement.querySelector("input");
      const icon = btn.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    });
  });

  if (isRegisterPage) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const pass = document.getElementById("password").value;
      const conf = document.getElementById("confirmPassword").value;

      if (pass !== conf) {
        alert("Konfirmasi password tidak cocok!");
        return;
      }

      const formData = new FormData(form);

      const res = await fetch("backend/register.php", {
        method: "POST",
        body: formData,
      });

      const text = await res.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch {
        alert("Server Error: " + text);
        return;
      }

      if (data.status === "success") {
        alert("Registrasi berhasil! Silakan login.");
        window.location.href = "login.php";
      } else {
        alert(data.message);
      }
    });
  } else {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = new FormData(form);

      const res = await fetch("backend/login.php", {
        method: "POST",
        body: formData,
      });

      const text = await res.text();
      let data;

      try {
        data = JSON.parse(text);
      } catch {
        alert("Server Error: " + text);
        return;
      }

      if (data.status === "success") {
        if (data.role === "admin") {
          window.location.href = "dashboard/admin/dashboard.php";
        } else {
          window.location.href = "dashboard/user/dashboard.php";
        }
      } else {
        alert(data.message || "Login gagal");
      }
    });
  }
});
