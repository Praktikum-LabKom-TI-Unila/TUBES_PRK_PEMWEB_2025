document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    if (!email || !password) {
        alert('Email dan Password harus diisi!');
        return;
    }

    // SIMULASI: Jika sukses, arahkan ke dashboard
    console.log(`Mengirim kredensial untuk: ${email}`);
    alert('Login berhasil! Mengarahkan ke dashboard...');
    window.location.href = 'admin-dashboard.php';
});