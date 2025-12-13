// 1. Fungsi Konfirmasi Hapus User
function confirmDelete(id, nama) {
    // Tampilkan popup konfirmasi yang lebih informatif
    if (confirm(`⚠️ PERINGATAN PENTING!\n\nApakah Anda yakin ingin menghapus Toko "${nama}"?\n\nPERHATIAN: Tindakan ini akan menghapus permanen:\n- Akun User\n- Semua Produk Toko ini\n- Semua Riwayat Bundle/Kolaborasi Toko ini\n\nLanjutkan menghapus?`)) {
        // Jika user klik OK, arahkan ke file proses dengan parameter hapus user
        window.location.href = `proses_admin.php?aksi=hapus_user&id=${id}`;
    }
}

// 2. Fungsi Konfirmasi Hapus Voucher (BARU)
function confirmDeleteVoucher(id, kode) {
    // Tampilkan popup konfirmasi sederhana untuk voucher
    if (confirm(`Yakin ingin menghapus data voucher "${kode}" dari laporan?\nData yang dihapus tidak dapat dikembalikan.`)) {
        // Jika user klik OK, arahkan ke file proses dengan parameter hapus voucher
        window.location.href = `proses_admin.php?aksi=hapus_voucher&id=${id}`;
    }
}

// 3. Fungsi Print Laporan
// Fungsi ini memanggil dialog print bawaan browser
function printLaporan() {
    window.print();
}

// 4. Auto Hide Alert (Notifikasi hilang sendiri setelah 3 detik)
// Berguna agar tampilan admin tetap bersih setelah ada pesan sukses/gagal
document.addEventListener("DOMContentLoaded", function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            // Tambahkan class fade agar transparan pelan-pelan (jika menggunakan Bootstrap)
            alert.classList.add('fade');
            // Hapus elemen dari DOM setelah animasi fade selesai (500ms)
            setTimeout(() => alert.remove(), 500); 
        }, 3000); // Tunggu 3 detik sebelum mulai menghilang
    });
});