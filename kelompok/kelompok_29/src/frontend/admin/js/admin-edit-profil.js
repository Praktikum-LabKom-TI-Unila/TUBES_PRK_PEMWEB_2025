document.getElementById('formEditProfile').addEventListener('submit', function(event) {
    event.preventDefault(); 
    
    // 1. Validasi Sederhana 
    const nama = document.getElementById('nama').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('telepon').value;

    if (!nama || !email || !phone) {
        alert('Semua field bertanda (*) wajib diisi!');
        return;
    }

    // 2. Kumpulkan Data
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // 3. --- LOGIKA BACKEND DI SINI ---
    // Di sini akan menggunakan fetch() untuk mengirim data (data) ke endpoint PHP
    
    console.log("Data profile siap dikirim:", data);
    
    // 4. Simulasi Notifikasi Sukses 
    displaySuccessNotification('Profile berhasil diperbarui!');
    
    // Setelah simulasi berhasil, arahkan kembali ke Dashboard
    setTimeout(() => {
        window.location.href = 'admin-dashboard.php';
    }, 2500); 
});


function displaySuccessNotification(message) {
    const notif = document.createElement('div');
    notif.className = 'success-notification';
    notif.innerHTML = `
        <span class="icon">✅</span>
        ${message}
    `;
    
    const existingNotif = document.querySelector('.success-notification');
    if (existingNotif) existingNotif.remove();

    document.body.appendChild(notif);

    const style = document.createElement('style');
    style.innerHTML = `
        .success-notification {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background-color: #e6ffed; border: 1px solid #c3e6cb;
            color: #155724; padding: 15px 30px; border-radius: 8px;
            font-weight: 600; display: flex; align-items: center; gap: 15px;
            z-index: 10000; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-out, fadeOut 0.5s ease-in 2s forwards;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translate(-50%, -20px); } to { opacity: 1; transform: translate(-50%, 0); } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    `;
    document.head.appendChild(style);

    setTimeout(() => {
        const currentNotif = document.querySelector('.success-notification');
        if (currentNotif) currentNotif.remove();
        style.remove();
    }, 3000);
}