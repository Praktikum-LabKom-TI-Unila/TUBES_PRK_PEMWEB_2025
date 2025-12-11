// File: js/warga-login.js

document.getElementById('wargaLoginForm').addEventListener('submit', (e) => {
    e.preventDefault(); 
    
    // Di sini akan ada fetch() ke endpoint PHP untuk validasi kredensial.
    
    alert('Login berhasil! Mengarahkan ke dashboard warga...');
    window.location.href = 'warga-dashboard.php';
});