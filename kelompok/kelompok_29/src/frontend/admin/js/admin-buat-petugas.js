document.getElementById('formPetugasBaru').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // 1. Ambil Nilai
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('konfirmasiPassword').value;

    // 2. Validasi
    if (password !== confirmPassword) {
        alert('Password dan Konfirmasi Password tidak cocok!');
        return;
    }
    if (password.length < 6) {
        alert('Password minimal 6 karakter!');
        return;
    }
    
    // 3. Kumpulkan Data (untuk fetch() di backend)
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    console.log("Data petugas baru siap dikirim:", data);
    
    // 4. Simulasi Notifikasi Sukses (Menggunakan JS Native)
    displaySuccessNotification('Akun petugas berhasil dibuat!');
    
    setTimeout(() => {
        window.location.href = 'admin-kelola-petugas.php';
    }, 2500); 
});


function displaySuccessNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'success-notification fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-100 text-green-800 border border-green-300 px-6 py-3 rounded-lg font-semibold shadow-lg flex items-center gap-3 z-50';
    notif.innerHTML = `<i class="material-icons text-xl">check_circle</i> ${message}`;
    
    const existingNotif = document.querySelector('.success-notification');
    if (existingNotif) existingNotif.remove();

    document.body.appendChild(notif);
    
    setTimeout(() => {
        const currentNotif = document.querySelector('.success-notification');
        if (currentNotif) currentNotif.remove();
    }, 3000);
}