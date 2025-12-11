<?php
// DUMMY DATA TUGAS
$taskId = isset($_GET['id']) ? $_GET['id'] : 'TKT-001';
$task = [
    'id' => $taskId,
    'reporterName' => 'Ahmad Wijaya',
    'title' => 'Jalan Berlubang di Jl. Sudirman',
    'category' => 'Jalan Raya',
    'status' => 'ditugaskan-ke-petugas', // Ganti status untuk testing aksi: 'dalam-proses', 'selesai'
    'createdAt' => '2025-10-10',
    'location' => 'Jl. Sudirman No. 45, Jakarta Pusat',
    'description' => 'Terdapat lubang yang cukup dalam dan lebar di tengah jalan yang berpotensi menyebabkan kecelakaan.',
    'imageUrl' => 'img/dummy-road-initial.jpg',
    'completionImageUrl' => 'img/dummy-road-fixed.jpg', // Tampilkan jika ada
    'officerNotes' => 'Perbaikan selesai dengan penambalan aspal.',
    'timeline' => [
        // Dummy Timeline
    ]
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
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto p-4 flex items-center gap-3">
            <a href="petugas-task-list.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">&larr;</a>
            <div class="flex-1">
                <h1 class="text-xl font-semibold text-gray-900">Detail Tugas</h1>
                <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($task['id']); ?></p>
            </div>
            <div id="statusBadgeContainer"></div>
        </div>
    </div>

    <main class="py-8">
        <div class="max-w-4xl mx-auto p-4 space-y-4">
            
            <div class="bg-white rounded-xl p-4 border border-gray-200">
                <div class="flex flex-wrap gap-3" id="actionButtonsContainer">
                    </div>
            </div>

            <div class="bg-white rounded-xl overflow-hidden border border-gray-200">
                <img src="<?php echo htmlspecialchars($task['imageUrl']); ?>" alt="<?php echo htmlspecialchars($task['title']); ?>" class="w-full h-64 object-cover">
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h2 class="mb-4 text-2xl font-semibold"><?php echo htmlspecialchars($task['title']); ?></h2>

                <div class="flex flex-wrap gap-4 text-gray-600 mb-6 text-sm">
                    <div class="flex items-center gap-2"><i class="material-icons text-base">calendar_today</i><span><?php echo date('d F Y', strtotime($task['createdAt'])); ?></span></div>
                    <div class="flex items-center gap-2"><i class="material-icons text-base">description</i><span><?php echo htmlspecialchars($task['category']); ?></span></div>
                </div>

                <div class="mb-4">
                    <p class="text-gray-700 mb-2 font-medium">Deskripsi Masalah:</p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($task['description']); ?></p>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <div class="flex items-start gap-2">
                        <i class="material-icons text-xl text-gray-400 mt-0.5">location_on</i>
                        <div>
                            <p class="text-gray-700 mb-1 font-medium">Lokasi:</p>
                            <p class="text-gray-600"><?php echo htmlspecialchars($task['location']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h3 class="mb-4 text-xl font-semibold">Peta Lokasi</h3>
                <div class="map-placeholder h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    [Placeholder Peta Lokasi]
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h3 class="mb-4 text-xl font-semibold">Informasi Pelapor</h3>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                        <i class="material-icons">email</i>
                    </div>
                    <div>
                        <p class="text-gray-900 mb-1"><?php echo htmlspecialchars($task['reporterName']); ?></p>
                        <p class="text-gray-600"><?php echo htmlspecialchars($task['reporterEmail']); ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($task['completionImageUrl']): ?>
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
                <div class="flex items-center gap-2 mb-4">
                    <i class="material-icons text-xl text-green-600">check_circle</i>
                    <h3 class="text-green-900 text-xl font-semibold">Bukti Penyelesaian</h3>
                </div>
                
                <img src="<?php echo htmlspecialchars($task['completionImageUrl']); ?>" alt="Completion" class="w-full h-64 object-cover rounded-lg mb-4" />
                
                <?php if ($task['officerNotes']): ?>
                <div class="bg-white p-4 rounded-lg">
                    <p class="text-gray-700 mb-1 font-medium">Catatan:</p>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($task['officerNotes']); ?></p>
                </div>
                <?php endif; ?>

                <a href="petugas-upload-proof.php?id=<?php echo htmlspecialchars($task['id']); ?>"
                    class="mt-4 w-full bg-white border border-green-600 text-green-600 py-3 rounded-lg hover:bg-green-50 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons text-xl">edit</i> Edit Bukti Penyelesaian
                </a>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h3 class="mb-4 text-xl font-semibold">Timeline</h3>
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