<?php $complaintId = isset($_GET['id']) ? $_GET['id'] : ''; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan #<?php echo htmlspecialchars($complaintId); ?></title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
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
            <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($complaintId ?: '-'); ?></p>
          </div>
          <div id="statusBadgeContainer">
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-4 space-y-6">
        <div id="detailAlert" class="hidden p-3 rounded-lg text-sm"></div>
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-hidden">
                <img id="detailPhotoBefore" src="" alt="Foto Pengaduan" class="w-full h-64 object-cover bg-gray-100" />
            </div>
            
            <div class="p-6">
                <h2 id="detailTitle" class="text-xl font-semibold text-gray-900 mb-2">-</h2>
                
                <div class="flex flex-wrap gap-4 text-gray-600 mb-4 text-sm">
                    <div class="flex items-center gap-1">
                        <span id="detailCreatedAt" class="font-medium text-xs text-gray-500">-</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span id="detailCategory" class="text-xs font-medium text-gray-500">-</span>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 font-medium mb-1">Deskripsi:</p>
                    <p id="detailDescription" class="text-gray-600 text-sm">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm space-y-4">
            <div class="flex items-start gap-2">
                <i class="material-icons text-lg text-gray-400 mt-0.5">place</i>
                <div>
                    <p class="text-gray-700 font-medium mb-1">Lokasi</p>
                    <p id="detailAddress" class="text-gray-600 text-sm">-</p>
                </div>
            </div>
            <div id="detailMap" class="h-48 bg-gray-200 rounded-lg"></div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
            <h3 class="text-xl font-semibold mb-4">Timeline Progress</h3>
            <div id="timelineContainer" class="timeline-container">
                </div>
        </div>

        <div id="assignedOfficerSection" class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hidden">
            <h3 class="text-xl font-semibold mb-4">Petugas yang Menangani</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-xl text-gray-600">
                    <i class="material-icons text-lg">person</i>
                </div>
                <div>
                    <p id="officerName" class="text-gray-900 font-medium">-</p>
                    <p id="officerMeta" class="text-gray-500 text-sm">-</p>
                </div>
            </div>
        </div>

        <div id="completionSection" class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm hidden">
            <h3 class="text-xl font-semibold mb-4">Bukti Penyelesaian</h3>
            <div id="completionProofs" class="space-y-3"></div>
        </div>
    </div>
    
    <script src="js/warga-auth.js"></script>
    <script>
        PelaporAuth.requirePelapor();
    </script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        window.COMPLAINT_ID = <?php echo json_encode($complaintId); ?>;
    </script>
    <script src="js/warga-detail-pengaduan.js"></script>
</body>
</html>
