// Login Page Logic - Simple & Clean

function handleLogin(event) {
  event.preventDefault();

  const form = event.target;
  const email = form.email.value;
  const password = form.password.value;
  const submitBtn = form.querySelector('button[type="submit"]');

  // Show loading
  submitBtn.disabled = true;
  submitBtn.textContent = "Memproses...";
  hideMessages();

  // Prepare data
  const data = new URLSearchParams();
  data.append("email", email);
  data.append("password", password);

  // Call API
  fetch(API_URL + "/login", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    credentials: "include",
    body: data,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("Login response:", data); // Debug

      if (data.success) {
        // Store user data in sessionStorage
        if (data.data) {
          sessionStorage.setItem("user", JSON.stringify(data.data));
        }

        showSuccess("Login berhasil! Mengalihkan...");
        setTimeout(() => {
          // Redirect based on role
          if (data.data && data.data.role === "MAHASISWA") {
            window.location.href = "pages/Mahasiswa/dashboardMahasiswa.html";
          } else if (data.data && data.data.role === "PETUGAS") {
            window.location.href = "pages/Petugas/dashboardPetugas.html";
          } else if (data.data && data.data.role === "ADMIN") {
            window.location.href = "pages/Admin/dashboardAdmin.html";
          } else {
            window.location.href = "index.html";
          }
        }, 1000);
      } else {
        showError(
          data.message || "Login gagal. Periksa email dan password Anda."
        );
        submitBtn.disabled = false;
        submitBtn.textContent = "Masuk";
      }
    })
    .catch((error) => {
      console.error("Login error:", error);
      showError("Terjadi kesalahan. Pastikan backend sudah berjalan.");
      submitBtn.disabled = false;
      submitBtn.textContent = "Masuk";
    });
}

function showError(message) {
  const errorDiv = document.getElementById("loginError");
  errorDiv.textContent = message;
  errorDiv.classList.remove("hidden");
}

function showSuccess(message) {
  const successDiv = document.getElementById("loginSuccess");
  successDiv.textContent = message;
  successDiv.classList.remove("hidden");
}

function hideMessages() {
  document.getElementById("loginError").classList.add("hidden");
  document.getElementById("loginSuccess").classList.add("hidden");
}
