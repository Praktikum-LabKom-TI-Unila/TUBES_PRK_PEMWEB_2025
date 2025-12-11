<?php
// DUMMY DATA TUGAS
$taskId = isset($_GET['id']) ? $_GET['id'] : 'TKT-001';
$task = [
    'id' => $taskId,
    'reporterName' => 'Ahmad Wijaya',
    'reporterEmail' => 'ahmad.wijaya@email.com',
    'title' => 'Jalan Berlubang di Jl. Sudirman',
    'category' => 'Jalan Raya',
    'status' => 'dalam-proses', // Status untuk menguji tampilan (Sesuai 25d3505d-adb5-471a-ba67-f726e9e284e2.jpg)
    'createdAt' => '2025-12-05', 
    'location' => 'Jl. Sudirman No. 45, Jakarta Pusat',
    'description' => 'Terdapat lubang besar di tengah jalan yang sangat berbahaya bagi pengendara motor. Lubang berdiameter sekitar 50cm dan kedalaman 10cm.',
    'imageUrl' => 'img/dummy-road-initial.jpg',
    'completionImageUrl' => null, 
    'officerNotes' => '',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas #<?php echo htmlspecialchars($taskId); ?></title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .map-placeholder { background-color: #f0f0f0; }
        .header-sticky { background-color: white; border-bottom: 1px solid #e9ecef; position: sticky; top: 0; z-index: 10; }
        /* Timeline - Diambil dari styling Detail Tiket Admin */
        .timeline-wrapper { position: relative; padding-left: 10px; }
        .timeline-point { width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; position: absolute; left: 0; transform: translateX(-50%); z-index: 5; }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="header-sticky">
        <div class="max-w-4xl mx-auto p-4 flex items-center justify-between">
            <a href="petugas-task-list.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <h1 class="text-xl font-semibold text-gray-900 flex-1">Detail Tugas</h1>
            <div id="statusBadgeContainer"></div>
        </div>
    </div>

    <main class="py-8">
        <div class="max-w-4xl mx-auto p-4 space-y-4">
            
            <div class="flex flex-wrap gap-3">
                <div id="actionButtonsContainer" class="flex flex-wrap gap-3 w-full">
                    </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="overflow-hidden rounded-t-xl">
                    <img src="<?php echo htmlspecialchars($task['imageUrl']); ?>" alt="<?php echo htmlspecialchars($task['title']); ?>" class="w-full h-64 object-cover">
                </div>
                
                <div class="p-4">
                    <div class="mb-3 text-sm text-gray-600">
                        <span class="font-medium text-gray-800"><?php echo htmlspecialchars($task['title']); ?></span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex items-center gap-1"><i class="material-icons text-base text-gray-500">calendar_today</i> <span><?php echo date('d M Y', strtotime($task['createdAt'])); ?></span></div>
                            <span class="text-gray-400">|</span>
                            <div class="flex items-center gap-1"><i class="material-icons text-base text-gray-500">description</i> <span><?php echo htmlspecialchars($task['category']); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Deskripsi Masalah</p>
                <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($task['description']); ?></p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Lokasi</p>
                <p class="text-gray-600 text-sm flex items-center gap-2">
                    <i class="material-icons text-base text-gray-500">place</i>
                    <?php echo htmlspecialchars($task['location']); ?>
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-4">Peta Lokasi</p>
                <div class="map-placeholder h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    [Placeholder Peta Lokasi]
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-2">Informasi Pelapor</p>
                <p class="text-gray-900 text-sm"><?php echo htmlspecialchars($task['reporterName']); ?></p>
                <p class="text-gray-600 text-xs"><?php echo htmlspecialchars($task['reporterEmail']); ?></p>
            </div>
            
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <p class="font-semibold text-gray-800 mb-4">Timeline Progress</p>
                <div id="timelineContainer">
                    </div>
            </div>
        </div>
    </main>

    <script src="js/petugas-task-detail.js"></script>
    <script>
        window.taskData = <?php echo json_encode($task); ?>;
    </script>
</body>
</html>