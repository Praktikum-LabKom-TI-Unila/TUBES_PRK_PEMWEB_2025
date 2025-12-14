<?php
// Dummy data untuk validasi (diambil dari data tiket sebelumnya)
$ticketId = isset($_GET['id']) ? $_GET['id'] : 'TKT-001';
$validationTicket = [
    'id' => $ticketId,
    'title' => 'Jalan Berlubang di Jl. Sudirman',
    'imageUrl' => 'img/dummy-road-initial.jpg',
    'description' => 'Terdapat lubang yang cukup dalam dan lebar di tengah jalan.',
    'completionImageUrl' => 'img/dummy-road-fixed.jpg',
    'officerNotes' => 'Perbaikan selesai dengan penambalan aspal pada area lubang utama.',
    'reporterName' => 'Ahmad Wijaya',
    'category' => 'Jalan Raya',
    'assignedOfficer' => 'Budi Santoso',
    'location' => 'Jl. Sudirman No. 45, Jakarta Pusat',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Penyelesaian #<?php echo htmlspecialchars($ticketId); ?></title>
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/admin-validasi.css"> 
    <script src="js/admin-auth-guard.js"></script>
    <style>
        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: #fecaca;
        }
    </style>
</head>
<body>
    
    <div class="header-sticky">
        <div class="container max-w-4xl mx-auto p-4 flex items-center gap-3">
            <a href="admin-detail-tiket.php?id=<?php echo htmlspecialchars($ticketId); ?>" class="back-link">&larr;</a>
            <h1 class="mr-auto">Validasi Penyelesaian #<?php echo htmlspecialchars($ticketId); ?></h1>
            <button
                type="button"
                class="btn-logout"
                data-admin-logout
                data-default-text="Logout"
                data-loading-text="Keluar..."
            >
                <span aria-hidden="true">↪</span>
                <span>Logout</span>
            </button>
        </div>
    </div>

    <main class="content-wrapper">
        <div class="container max-w-4xl mx-auto p-4 space-y-4">
            
            <div class="alert alert-blue-info">
                <p>📝 Review Penyelesaian Tiket</p>
                <p class="text-sm">Periksa bukti penyelesaian yang diunggah petugas sebelum menutup tiket ini.</p>
            </div>

            <div class="card p-6 border">
                <h2 class="mb-4 text-xl">Foto Kondisi Awal</h2>
                <img src="<?php echo htmlspecialchars($validationTicket['imageUrl']); ?>" alt="Kondisi awal" class="w-full h-80 object-cover rounded-xl">
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="font-semibold mb-2"><?php echo htmlspecialchars($validationTicket['title']); ?></p>
                    <p class="text-muted text-sm"><?php echo htmlspecialchars($validationTicket['description']); ?></p>
                </div>
            </div>

            <div class="card p-6 border">
                <h2 class="mb-4 text-xl">Foto Bukti Penyelesaian</h2>
                <img src="<?php echo htmlspecialchars($validationTicket['completionImageUrl']); ?>" alt="Bukti penyelesaian" class="w-full h-80 object-cover rounded-xl">
                
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <p class="font-semibold mb-2">Catatan Petugas:</p>
                    <p class="text-muted text-sm"><?php echo htmlspecialchars($validationTicket['officerNotes']); ?></p>
                </div>
            </div>

            <div class="card p-6 border">
                <h2 class="mb-4 text-xl">Perbandingan Sebelum & Sesudah</h2>
                <div class="grid-2-cols gap-4">
                    <div>
                        <p class="font-semibold mb-2">Sebelum</p>
                        <img src="<?php echo htmlspecialchars($validationTicket['imageUrl']); ?>" alt="Sebelum" class="w-full h-48 object-cover rounded-lg">
                    </div>
                    <div>
                        <p class="font-semibold mb-2">Sesudah</p>
                        <img src="<?php echo htmlspecialchars($validationTicket['completionImageUrl']); ?>" alt="Sesudah" class="w-full h-48 object-cover rounded-lg">
                    </div>
                </div>
            </div>

            <div class="card p-6 border">
                <h2 class="mb-4 text-xl">Catatan Validasi</h2>
                <form id="validationForm">
                    <textarea rows="4" id="adminNotes" placeholder="Tambahkan catatan untuk keputusan validasi (opsional)..." class="w-full p-3 border rounded-lg resize-none mb-4"></textarea>

                    <div class="grid-2-cols gap-4">
                        <button type="button" onclick="handleRejectValidation('<?php echo htmlspecialchars($ticketId); ?>')" class="btn-reject">
                            ❌ Tolak & Minta Revisi
                        </button>
                        <button type="button" onclick="handleApproveValidation('<?php echo htmlspecialchars($ticketId); ?>')" class="btn-approve">
                            ✅ Setujui Penyelesaian
                        </button>
                    </div>
                </form>
            </div>

            <div class="card p-6 border">
                <h3 class="mb-4">Ringkasan Tiket</h3>
                <div class="grid-2-cols gap-4 summary-info">
                    <div><p class="text-muted mb-1">ID Tiket</p><p class="text-dark">#<?php echo htmlspecialchars($validationTicket['id']); ?></p></div>
                    <div><p class="text-muted mb-1">Pelapor</p><p class="text-dark"><?php echo htmlspecialchars($validationTicket['reporterName']); ?></p></div>
                    <div class="span-2-cols"><p class="text-muted mb-1">Lokasi</p><p class="text-dark"><?php echo htmlspecialchars($validationTicket['location']); ?></p></div>
                </div>
            </div>
        </div>
    </main>

    <script src="js/admin-logout.js"></script>
    <script src="js/admin-validasi-selesai.js"></script>
</body>
</html>