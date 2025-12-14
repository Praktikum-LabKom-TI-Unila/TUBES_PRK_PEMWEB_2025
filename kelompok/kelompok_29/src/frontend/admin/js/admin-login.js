document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // Logika validasi dan pengiriman data (AJAX fetch) akan ditaruh di sini.
    
    // Simulasi Sukses:
    alert('Login berhasil! Mengarahkan ke dashboard...');
    window.location.href = 'admin-dashboard.php';
});