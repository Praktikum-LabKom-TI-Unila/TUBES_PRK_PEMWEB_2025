const loginForm = document.getElementById('wargaLoginForm');
const loginButton = document.getElementById('loginButton');
const loginFeedback = document.getElementById('loginFeedback');

function setLoginFeedback(message, isError = true) {
    if (!loginFeedback) return;
    loginFeedback.textContent = message || '';
    loginFeedback.classList.remove('text-red-600', 'text-green-600');
    loginFeedback.classList.add(isError ? 'text-red-600' : 'text-green-600');
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof PelaporAuth !== 'undefined') {
        PelaporAuth.redirectToDashboard();
    }
});

loginForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!email || !password) {
        setLoginFeedback('Email dan password wajib diisi.');
        return;
    }

    setLoginFeedback('');
    loginButton.disabled = true;
    loginButton.textContent = 'Memeriksa...';

    try {
        const response = await fetch(`${PelaporAuth.API_BASE_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email,
                identifier: email,
                password
            })
        });
        const payload = await response.json();

        if (!response.ok || payload?.status === 'error') {
            const message = payload?.message || 'Login gagal.';
            const detail = Array.isArray(payload?.errors)
                ? payload.errors.map(err => err?.message).filter(Boolean).join(', ')
                : '';
            const composed = detail ? `${message} (${detail})` : message;
            setLoginFeedback(composed);
            throw new Error(composed);
        }

        const user = payload?.data?.user;
        const role = (user?.role || '').toLowerCase();
        if (role !== 'pelapor' && role !== 'warga') {
            setLoginFeedback('Hanya pelapor yang bisa masuk ke sini.');
            return;
        }

        const token = payload?.data?.token;
        if (!token) {
            throw new Error('Token tidak ditemukan, silakan coba lagi.');
        }

        PelaporAuth.setSession(token, user);
        PelaporAuth.redirectToDashboard();
    } catch (error) {
        console.error('[PelaporLogin] Error', error);
        setLoginFeedback(error.message || 'Tidak dapat login saat ini.');
    } finally {
        loginButton.disabled = false;
        loginButton.textContent = 'Masuk';
    }
});
