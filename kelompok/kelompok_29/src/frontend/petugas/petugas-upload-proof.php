<?php
// DUMMY DATA
$taskId = isset($_GET['id']) ? $_GET['id'] : 'TKT-001';
$task = [
    'id' => $taskId,
    'title' => 'Jalan Berlubang di Jl. Sudirman',
    'category' => 'Jalan Raya',
    'location' => 'Jl. Sudirman No. 45, Jakarta Pusat',
    'imageUrl' => 'img/dummy-road-initial.jpg',
    'completionImageUrl' => null, // null untuk testing upload pertama, string untuk testing edit
    'officerNotes' => '',
];
$isEdit = !empty($task['completionImageUrl']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bukti Penyelesaian</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50">
    
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto p-4 flex items-center gap-3">
            <a href="petugas-task-detail.php?id=<?php echo htmlspecialchars($task['id']); ?>" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Upload Bukti Penyelesaian</h1>
                <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($task['id']); ?></p>
            </div>
        </div>
    </div>

    <main class="py-8">
        <div class="max-w-4xl mx-auto p-4">
            <form id="uploadProofForm" class="space-y-6">
                
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-700 mb-3">Tugas yang Diselesaikan</h2>
                    <div class="flex gap-4">
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                            <img src="<?php echo htmlspecialchars($task['imageUrl']); ?>" alt="<?php echo htmlspecialchars($task['title']); ?>" class="w-full h-full object-cover" />
                        </div>
                        <div>
                            <p class="text-gray-900 font-medium mb-1"><?php echo htmlspecialchars($task['title']); ?></p>
                            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($task['category']); ?></p>
                            <p class="text-gray-500 text-xs mt-1"><?php echo htmlspecialchars($task['location']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Foto Kondisi Sebelum</h3>
                    <img src="<?php echo htmlspecialchars($task['imageUrl']); ?>" alt="Sebelum" class="w-full h-64 object-cover rounded-lg" />
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <label class="block text-gray-900 font-medium mb-2 text-sm">
                        Foto Bukti Penyelesaian <span class="text-red-500">*</span>
                    </label>
                    <p class="text-gray-600 mb-4 text-xs">
                        Upload foto kondisi infrastruktur setelah diperbaiki
                    </p>
                    
                    <div id="imagePreviewContainer">
                        </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <label for="notes" class="block text-gray-900 font-medium mb-2 text-sm">
                        Catatan Penyelesaian <span class="text-red-500">*</span>
                    </label>
                    <p class="text-gray-600 mb-4 text-xs">
                        Jelaskan pekerjaan yang telah dilakukan
                    </p>
                    <textarea
                        id="notes"
                        rows="5"
                        placeholder="Contoh: Lubang telah ditutup dengan aspal, area dibersihkan, dan sudah aman untuk dilalui kendaraan..."
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none"
                        required
                    ><?php echo htmlspecialchars($task['officerNotes']); ?></textarea>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-900">
                    <div class="flex gap-3">
                        <i class="material-icons text-xl text-blue-600 flex-shrink-0 mt-0.5">info</i>
                        <div>
                            <p class="font-medium mb-1">Informasi Penting</p>
                            <p class="text-blue-700 text-xs">Setelah submit, tugas akan ditandai sebagai menunggu validasi admin. Pastikan foto dan catatan sudah sesuai sebelum mengirim.</p>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        onclick="window.location.href='petugas-task-detail.php?id=<?php echo htmlspecialchars($task['id']); ?>'"
                        class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 font-semibold"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold"
                    >
                        <i class="material-icons text-xl">check_circle</i> Kirim Bukti Penyelesaian
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="js/petugas-upload-proof.js"></script>
    <script>
        // Data PHP di-pass ke JavaScript
        window.taskInitialData = {
            id: '<?php echo $task['id']; ?>',
            completionImageUrl: <?php echo json_encode($task['completionImageUrl']); ?>,
            imageUrlBefore: '<?php echo $task['imageUrl']; ?>',
            officerNotes: '<?php echo $task['officerNotes']; ?>'
        };
    </script>
</body>
</html>