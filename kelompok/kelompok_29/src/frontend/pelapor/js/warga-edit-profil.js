// File: js/warga-edit-profil.js

document.getElementById('formEditProfileWarga').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // Validasi sederhana
    const nikInput = document.querySelector('[name="nik"]').value;
    if (nikInput.length !== 16 || isNaN(nikInput)) {
        alert('NIK harus 16 digit angka!');
        return;
    }

    // Ambil Data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    console.log("Data profile warga siap dikirim:", data);
    
    // Simulasi Notifikasi Sukses
    displaySuccessNotification('Profile berhasil diperbarui!');
    
    setTimeout(() => {
        window.location.href = 'warga-dashboard.php';
    }, 2500); 
});


function displaySuccessNotification(message) {
    // Menggunakan Tailwind classes untuk notifikasi sukses
    const notif = document.createElement('div');
    notif.className = 'fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-100 text-green-800 border border-green-300 px-6 py-3 rounded-lg font-semibold shadow-lg flex items-center gap-3 z-50';
    notif.innerHTML = `<i class="material-icons text-xl">check_circle</i> ${message}`;
    
    const existingNotif = document.querySelector('.success-notification');
    if (existingNotif) existingNotif.remove();

    document.body.appendChild(notif);
    
    setTimeout(() => {
        const currentNotif = document.querySelector('.success-notification');
        if (currentNotif) currentNotif.remove();
    }, 3000);
}