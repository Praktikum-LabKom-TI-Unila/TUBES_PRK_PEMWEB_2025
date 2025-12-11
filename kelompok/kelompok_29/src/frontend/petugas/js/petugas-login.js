const form = document.getElementById('petugasLoginForm');
const button = document.getElementById('petugasLoginButton');
const feedback = document.getElementById('loginFeedback');

function setFeedback(message, isError = true) {
    if (!feedback) return;
    feedback.textContent = message;
    feedback.classList.remove('text-red-600', 'text-green-600');
    feedback.classList.add(isError ? 'text-red-600' : 'text-green-600');
}

PetugasAuth.redirectToDashboard();

form.addEventListener('submit', async function(event) {
    event.preventDefault();

    const identifier = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (!identifier || !password) {
        setFeedback('Email dan password wajib diisi.');
        return;
    }

    button.disabled = true;
    button.textContent = 'Memeriksa...';
    setFeedback('');

    try {
        const response = await fetch(`${PetugasAuth.API_BASE_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: identifier,
                identifier,
                password
            })
        });

        const payload = await response.json();

        if (response.ok && payload?.status === 'success') {
            const user = payload.data?.user;
            const role = user?.role;
            if (role !== 'officer') {
                setFeedback('Hanya petugas lapangan yang bisa login.');
                return;
            }

            const token = payload.data?.token;
            if (!token) {
                setFeedback('Token tidak ditemukan, silakan coba lagi.');
                return;
            }

            PetugasAuth.setSession(token, user);
            PetugasAuth.redirectToDashboard();
            return;
        }

        const errorMessage = payload?.message || 'Gagal login, silakan ulangi.';
        setFeedback(errorMessage);
    } catch (error) {
        console.error('Error login petugas:', error);
        setFeedback('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
    } finally {
        button.disabled = false;
        button.textContent = 'Masuk ke Dashboard';
    }
});
