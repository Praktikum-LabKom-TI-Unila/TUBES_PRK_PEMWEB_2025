<?php $taskId = isset($_GET['id']) ? $_GET['id'] : ''; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas #<?php echo htmlspecialchars($taskId ?: '-'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <style>
        .map-placeholder { background-color: #f0f0f0; }
        .header-sticky { background-color: white; border-bottom: 1px solid #e9ecef; position: sticky; top: 0; z-index: 10; }
        .timeline-wrapper { position: relative; padding-left: 10px; }
        .timeline-point { width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; position: absolute; left: 0; transform: translateX(-50%); z-index: 5; }
        #taskMap { height: 250px; }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="header-sticky">
        <div class="max-w-4xl mx-auto p-4 flex items-center justify-between gap-4">
            <a href="petugas-task-list.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <div class="flex-1">
                <p class="text-sm text-gray-500">Detail Tugas</p>
                <h1 id="taskTitleHeading" class="text-xl font-semibold text-gray-900 truncate">#<?php echo htmlspecialchars($taskId ?: '-'); ?></h1>
            </div>
            <div id="statusBadgeContainer"></div>
        </div>
    </div>

    <main class="py-8">
        <div class="max-w-4xl mx-auto p-4 space-y-4">

            <div id="taskAlert" class="hidden p-3 rounded-lg text-sm"></div>
            
            <div class="flex flex-wrap gap-3">
                <div id="actionButtonsContainer" class="flex flex-wrap gap-3 w-full"></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="overflow-hidden rounded-t-xl">
                    <img id="taskHeroImage" src="" alt="Foto Pengaduan" class="w-full h-64 object-cover bg-gray-100">
                </div>
                
                <div class="p-4">
                    <div class="mb-3 text-sm text-gray-600">
                        <span id="taskTitle" class="font-medium text-gray-800">-</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex items-center gap-1"><i class="material-icons text-base text-gray-500">calendar_today</i> <span id="taskDate">-</span></div>
                            <span class="text-gray-400">|</span>
                            <div class="flex items-center gap-1"><i class="material-icons text-base text-gray-500">description</i> <span id="taskCategory">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Deskripsi Masalah</p>
                <p id="taskDescription" class="text-gray-600 text-sm">-</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Lokasi</p>
                <p id="taskAddress" class="text-gray-600 text-sm flex items-center gap-2">
                    <i class="material-icons text-base text-gray-500">place</i>
                    Lokasi belum tersedia
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-4">Peta Lokasi</p>
                <div id="mapAddressText" class="text-sm text-gray-500 mb-2">Menunggu koordinat dari server...</div>
                <div id="taskMap" class="map-placeholder h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    Lokasi belum tersedia
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Informasi Pelapor</p>
                <p id="reporterName" class="text-gray-900 text-sm">-</p>
                <p id="reporterEmail" class="text-gray-600 text-xs">-</p>
            </div>
            
            <div id="completionProofWrapper" class="hidden bg-white rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-semibold text-gray-800">Bukti Penyelesaian</p>
                    <span id="completionStatusLabel" class="text-green-600 text-sm font-medium"></span>
                </div>
                <img id="completionImage" src="" alt="Bukti Penyelesaian" class="w-full h-64 object-cover rounded-lg bg-gray-100 mb-3 hidden">
                <p id="completionNotes" class="text-gray-600 text-sm"></p>
                <button id="editProofButton" class="hidden mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold">
                    Edit Bukti Penyelesaian
                </button>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-4">Timeline Progress</p>
                <div id="timelineContainer"></div>
            </div>
        </div>
    </main>

    <script src="js/petugas-auth-guard.js"></script>
    <script>
        PetugasAuth.requireOfficer();
    </script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="js/petugas-task-detail.js"></script>
</body>
</html>
