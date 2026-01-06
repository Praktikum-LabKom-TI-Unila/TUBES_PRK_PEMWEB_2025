const registerForm = document.getElementById('registerForm');
const registerButton = document.getElementById('registerButton');
const registerFeedback = document.getElementById('registerFeedback');

function showRegisterFeedback(message, type = 'error') {
    if (!registerFeedback) return;
    if (!message) {
        registerFeedback.classList.add('hidden');
        registerFeedback.textContent = '';
        return;
    }
    const palette =
        type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-700';
    registerFeedback.className = `p-3 rounded-lg text-sm ${palette}`;
    registerFeedback.textContent = message;
    registerFeedback.classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof PelaporAuth !== 'undefined') {
        PelaporAuth.redirectToDashboard();
    }
});

registerForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    showRegisterFeedback('');

    const formData = new FormData(registerForm);
    const fullName = formData.get('name').trim();
    const email = formData.get('email').trim();
    const phone = formData.get('phone').trim();
    const nik = formData.get('nik').trim();
    const address = formData.get('address').trim();
    const password = formData.get('password').trim();
    const confirmPassword = formData.get('confirmPassword').trim();

    if (!fullName || !email || !phone || !nik || !address || !password || !confirmPassword) {
        showRegisterFeedback('Semua field wajib diisi.');
        return;
    }
    if (password.length < 8) {
        showRegisterFeedback('Password minimal 8 karakter.');
        return;
    }
    if (password !== confirmPassword) {
        showRegisterFeedback('Password dan konfirmasi tidak sama.');
        return;
    }
    if (!/^\d{16}$/.test(nik)) {
        showRegisterFeedback('NIK harus 16 digit angka.');
        return;
    }

    registerButton.disabled = true;
    registerButton.innerHTML =
        '<span class="flex items-center gap-2"><i class="material-icons animate-spin text-base">autorenew</i> Memproses...</span>';

    try {
        const response = await fetch(`${PelaporAuth.API_BASE_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                full_name: fullName,
                email,
                password,
                confirm_password: confirmPassword,
                phone,
                address,
                nik
            })
        });

        const payload = await response.json();
        if (!response.ok || payload?.status === 'error') {
            const message = payload?.message || 'Registrasi gagal.';
            const detail = Array.isArray(payload?.errors)
                ? payload.errors.map((err) => err?.message).filter(Boolean).join(', ')
                : '';
            throw new Error(detail ? `${message} (${detail})` : message);
        }

        showRegisterFeedback('Registrasi berhasil! Mengarahkan ke halaman login...', 'success');
        setTimeout(() => {
            window.location.href = 'warga-login.php';
        }, 1800);
    } catch (error) {
        console.error('[PelaporRegister] Error', error);
        showRegisterFeedback(error.message || 'Tidak dapat mendaftar sekarang.');
    } finally {
        registerButton.disabled = false;
        registerButton.innerHTML = '<i class="material-icons text-xl">person_add</i> Daftar Sekarang';
    }
});
