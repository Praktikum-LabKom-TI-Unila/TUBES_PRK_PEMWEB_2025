// File: js/petugas-edit-profil.js

document.getElementById('formEditProfilePetugas').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // Validasi
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('telepon').value;

    if (!nama || !email || !phone) {
        alert('Semua field bertanda (*) wajib diisi!');
        return;
    }

    // Kumpulkan Data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    console.log("Data profile petugas siap dikirim:", data);
    
    // 3. Simulasi Notifikasi Sukses (image_08d9d5.png)
    displaySuccessNotification('Profile berhasil diperbarui!');
    
    setTimeout(() => {
        window.location.href = 'petugas-task-list.php';
    }, 2500); 
});


function displaySuccessNotification(message) {
    // Menggunakan Tailwind classes untuk notifikasi hijau (image_08d9d5.png)
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