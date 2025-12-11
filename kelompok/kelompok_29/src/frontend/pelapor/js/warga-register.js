// File: js/warga-register.js

document.getElementById('registerForm').addEventListener('submit', (e) => {
    e.preventDefault(); 
    
    const pass = document.getElementById('password').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    
    // Validasi Password
    if (pass !== confirmPass) {
        alert('Password dan Konfirmasi Password tidak cocok!');
        return;
    }
    if (pass.length < 6) {
        alert('Password minimal 6 karakter!');
        return;
    }
    
    // Ambil data formulir
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    console.log('Data Registrasi:', data);

    // Di sini akan ada fetch() ke endpoint PHP untuk membuat akun baru.
    
    alert('Registrasi berhasil! Silakan login.');
    window.location.href = 'warga-login.php';
});