// --- 1. HANDLER VALIDASI ---

function handleApproveValidation(ticketId) {
    if (confirm('Yakin ingin MENYETUJUI penyelesaian ini? Tiket akan ditutup.')) {
        // Simulasi POST ke PHP (Approve)
        const notes = document.getElementById('adminNotes').value;
        console.log(`Menyetujui tiket ${ticketId} dengan catatan: ${notes}`);
        
        // Asumsi: Proses update status di backend PHP
        alert('Penyelesaian disetujui! Tiket telah ditutup.');
        window.location.href = `admin-detail-tiket.php?id=${ticketId}`; 
    }
}

function handleRejectValidation(ticketId) {
    if (confirm('Yakin ingin MENOLAK penyelesaian? Petugas akan diminta revisi.')) {
        // Simulasi POST ke PHP (Reject)
        const notes = document.getElementById('adminNotes').value;
        console.log(`Menolak tiket ${ticketId} dengan catatan: ${notes}`);
        
        // Asumsi: Proses update status di backend PHP
        alert('Penyelesaian ditolak. Status dikembalikan ke Dalam Proses.');
        window.location.href = `admin-detail-tiket.php?id=${ticketId}`; 
    }
}