<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Pengaduan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/admin-auth-guard.js"></script>
    <style>
        /* CSS Tambahan minimal untuk centering */
        .login-page-wrapper { min-height: 100vh; background-color: #f4f6f8; }
        /* Styling Icon Input */
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    </style>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* CSS Tambahan minimal untuk centering */
        .login-page-wrapper { min-height: 100vh; background-color: #f4f6f8; }
        /* Styling Icon Input */
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    </style>
</head>
<body>
    <div class="login-page-wrapper flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 text-white text-3xl">
                    <i class="material-icons">security</i>
                </div>
                <h1 class="text-xl font-semibold mb-2 text-gray-800">Sistem Pengaduan Infrastruktur</h1>
                <p class="text-gray-600">Portal Admin</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Masuk sebagai Admin</h2>

                <div
                    id="loginAlert"
                    class="hidden mb-4 text-sm px-4 py-3 rounded-lg border"
                    role="alert"
                    aria-live="polite"
                ></div>

                <form id="loginForm" action="admin-process_login.php" method="POST">
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Email Admin</label>
                        <div class="relative">
                            <i class="material-icons input-icon">mail</i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="admin@system.go.id"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                                required
                            />
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="material-icons input-icon">lock</i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                                required
                            />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                        data-default-text="Masuk ke Dashboard"
                    >
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-sm text-gray-700">
                <p>⚠️ Halaman ini hanya untuk admin sistem</p>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Resolusi otomatis URL backend dengan fallback eksplisit ke localhost:9090
            const resolveBackendBaseUrl = () => {
                const metaOverride = document.querySelector('meta[name="sipinda-backend-url"]');
                if (metaOverride?.content) {
                    return metaOverride.content.replace(/\/+$/, '');
                }

                const globalOverride = window.SIPINDA_BACKEND_URL || window.__SIPINDA_BACKEND_URL__;
                if (typeof globalOverride === "string" && globalOverride.trim()) {
                    return globalOverride.trim().replace(/\/+$/, '');
                }

                return "http://localhost:9090";
            };

            const API_BASE_URL = resolveBackendBaseUrl();
            const LOGIN_ENDPOINT = "/api/auth/login";
            const ADMIN_DASHBOARD_URL = "admin-dashboard.php";

            // Referensi elemen penting
            const form = document.getElementById("loginForm");
            const identifierInput = document.getElementById("email");
            const passwordInput = document.getElementById("password");
            const submitButton = form.querySelector("button[type='submit']");
            const alertBox = document.getElementById("loginAlert");
            const defaultBtnText = submitButton.dataset.defaultText || submitButton.textContent;

            // Fungsi utilitas untuk tampilkan notifikasi
            const showAlert = (message = "", type = "error") => {
                if (!message) {
                    alertBox.classList.add("hidden");
                    alertBox.textContent = "";
                    return;
                }

                const baseClass = "mb-4 text-sm px-4 py-3 rounded-lg border";
                const stateClass = type === "success"
                    ? "bg-green-50 border-green-200 text-green-700"
                    : "bg-red-50 border-red-200 text-red-700";

                alertBox.className = `${baseClass} ${stateClass}`;
                alertBox.textContent = message;
                alertBox.classList.remove("hidden");
            };

            // Fungsi pembantu untuk toggle loading tombol
            const setLoading = (isLoading) => {
                submitButton.disabled = isLoading;
                submitButton.textContent = isLoading ? "Memproses..." : defaultBtnText;
                submitButton.classList.toggle("opacity-70", isLoading);
                submitButton.classList.toggle("cursor-not-allowed", isLoading);
            };

            // Helper untuk parsing JSON aman (server kadang balas HTML saat salah tujuan)
            const safeParseJson = async (response) => {
                const rawText = await response.text();
                if (!rawText) return {};
                try {
                    return JSON.parse(rawText);
                } catch (error) {
                    return { __parseError: true, rawText };
                }
            };

            // Handler submit dengan async/await
            form.addEventListener("submit", async (event) => {
                event.preventDefault();

                const identifier = identifierInput.value.trim();
                const password = passwordInput.value.trim();

                // Validasi minimal sisi klien
                if (!identifier || !password) {
                    showAlert("Email/Nama pengguna dan password wajib diisi.");
                    return;
                }

                setLoading(true);
                showAlert();

                try {
                    const response = await fetch(`${API_BASE_URL}${LOGIN_ENDPOINT}`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        mode: "cors",
                        body: JSON.stringify({ identifier, password }),
                    });

                    const payload = await safeParseJson(response);

                    if (payload?.__parseError) {
                        showAlert("Server mengembalikan format tidak valid. Pastikan BASE URL sudah benar.");
                        return;
                    }

                    if (response.ok && payload?.status === "success") {
                        const user = payload?.data?.user || {};
                        const token = payload?.data?.token || "";
                        const userRole = (user.role || "").toLowerCase();

                        if (!token) {
                            showAlert("Respon login tidak menyertakan token. Hubungi admin aplikasi.");
                            return;
                        }

                        // Pastikan hanya role admin yang boleh lanjut
                        if (userRole !== "admin") {
                            showAlert("Akun Anda bukan admin. Hubungi administrator jika merasa ini kesalahan.");
                            return;
                        }

                        // Simpan token/user agar bisa dipakai fitur admin lain
                        localStorage.setItem("sipinda_admin_token", token);
                        sessionStorage.setItem("sipinda_admin_token", token);
                        localStorage.setItem("sipinda_admin_user", JSON.stringify(user));
                        sessionStorage.setItem("sipinda_admin_user", JSON.stringify(user));

                        showAlert(payload?.message || "Login admin berhasil.", "success");

                        setTimeout(() => {
                            window.location.href = ADMIN_DASHBOARD_URL;
                        }, 600);
                        return;
                    }

                    // Tampilkan pesan error dari API
                    const apiErrors = Array.isArray(payload?.errors) ? payload.errors : [];
                    const firstFieldError = apiErrors.find((err) => err?.message)?.message;
                    const errorMessage = firstFieldError || payload?.message || "Login gagal. Periksa kembali data Anda.";
                    showAlert(errorMessage);
                } catch (error) {
                    const message = (error?.message || "").toLowerCase();
                    let networkMsg = "Terjadi gangguan jaringan. Silakan coba lagi sesaat lagi.";

                    if (message.includes("failed to fetch") || message.includes("networkerror")) {
                        networkMsg = "Tidak bisa menjangkau server. Pastikan backend/ngrok sedang berjalan.";
                    } else if (message.includes("cors")) {
                        networkMsg = "Permintaan diblokir oleh CORS. Pastikan origin ini diizinkan server.";
                    }

                    showAlert(networkMsg);
                } finally {
                    setLoading(false);
                }
            });
        });
    </script>
</body>
</html>
