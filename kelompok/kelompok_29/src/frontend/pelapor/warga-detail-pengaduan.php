<?php
// Dummy data
$complaintId = isset($_GET['id']) ? $_GET['id'] : 'TKT-001';
$complaint = [
    'id' => $complaintId,
    'title' => 'Jalan Berlubang di Jl. Sudirman',
    'category' => 'Jalan Raya',
    'status' => 'dalam-proses', 
    'createdAt' => '2025-12-05',
    'location' => 'Jl. Sudirman No. 45, Jakarta Pusat',
    'description' => 'Terdapat lubang besar di tengah jalan yang sangat berbahaya bagi pengendara motor. Lubang berdiameter sekitar 50cm dan kedalaman 15cm.',
    'imageUrl' => 'img/dummy-construction.jpg', // Ganti dengan path gambar Anda
    'completionImageUrl' => '', // Kosongkan jika status belum selesai
    'assignedOfficer' => 'Budi Santoso',
    'reporterName' => 'Ahmad Wijaya',
    'adminNotes' => 'Sudah diverifikasi, segera ditindaklanjuti.', 
    'officerNotes' => '',
    'timeline' => [
        // Dummy timeline items (akan di-handle di JS)
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan #<?php echo htmlspecialchars($complaintId); ?></title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Styling tambahan untuk timeline, memastikan garis berada di kiri titik */
        .timeline-container { position: relative; }
        .timeline-line { 
            content: ''; 
            position: absolute; 
            top: 0; 
            bottom: 0; 
            left: 15px; /* Posisi garis di tengah ikon/dot */
            width: 2px; 
            background-color: #e0e0e0; 
            z-index: 0; 
        }
        .timeline-item { position: relative; z-index: 5; }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid white; z-index: 5; flex-shrink: 0; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <a href="warga-dashboard.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <p class="text-xl font-semibold text-gray-900">Detail Pengaduan</p>
            <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($complaint['id']); ?></p>
          </div>
          <div id="statusBadgeContainer">
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-4 space-y-6">
        
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-hidden">
                <img src="<?php echo htmlspecialchars($complaint['imageUrl']); ?>" alt="<?php echo htmlspecialchars($complaint['title']); ?>" class="w-full h-64 object-cover" />
            </div>
            
            <div class="p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2"><?php echo htmlspecialchars($complaint['title']); ?></h2>
                
                <div class="flex flex-wrap gap-4 text-gray-600 mb-4 text-sm">
                    <div class="flex items-center gap-1">
                        <span class="font-medium text-xs text-gray-500"><?php echo htmlspecialchars($complaint['createdAt']); ?></span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-medium text-gray-500"><?php echo htmlspecialchars($complaint['category']); ?></span>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 font-medium mb-1">Deskripsi:</p>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($complaint['description']); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <div class="flex items-start gap-2">
                <i class="material-icons text-lg text-gray-400 mt-0.5">place</i>
                <div>
                    <p class="text-gray-700 font-medium mb-1">Lokasi</p>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($complaint['location']); ?></p>
                </div>
            </div>
            <div class="h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                <div class="flex flex-col items-center">
                    <i class="material-icons text-4xl text-gray-400">location_on</i>
                    <p class="text-gray-500 text-sm mt-1"><?php echo htmlspecialchars($complaint['location']); ?></p>
                    <p class="text-xs text-gray-400">Klik untuk memilih lokasi pada peta (Placeholder)</p>
                </div>
            </div>
        </div>
        
        <?php if (!empty($complaint['adminNotes'])): ?>
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-xl font-semibold mb-4">Catatan</h3>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                <div class="flex items-center gap-2 mb-2">
                    <p class="text-blue-900 font-medium">Admin</p>
                </div>
                <p class="text-gray-700"><?php echo htmlspecialchars($complaint['adminNotes']); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-xl font-semibold mb-4">Timeline Progress</h3>
            <div id="timelineContainer" class="timeline-container">
                </div>
        </div>

        <?php if (!empty($complaint['assignedOfficer'])): ?>
          <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-xl font-semibold mb-4">Petugas yang Menangani</h3>
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-xl text-gray-600">
                <i class="material-icons text-lg">person</i>
              </div>
              <div>
                <p class="text-gray-900 font-medium"><?php echo htmlspecialchars($complaint['assignedOfficer']); ?></p>
                <p class="text-gray-500 text-sm">Petugas Lapangan</p>
              </div>
            </div>
          </div>
        <?php endif; ?>
    </div>
    
    <script>
        window.complaintData = <?php echo json_encode($complaint); ?>;
    </script>
    <script src="js/warga-detail-pengaduan.js"></script>
</body>
</html>