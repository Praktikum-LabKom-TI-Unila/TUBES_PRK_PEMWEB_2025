<?php $taskId = isset($_GET['id']) ? $_GET['id'] : ''; ?>
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
            <a href="petugas-task-detail.php?id=<?php echo htmlspecialchars($taskId); ?>" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Upload Bukti Penyelesaian</h1>
                <p class="text-gray-600 text-sm">#<?php echo htmlspecialchars($taskId ?: '-'); ?></p>
            </div>
        </div>
    </div>

    <main class="py-8">
        <div class="max-w-4xl mx-auto p-4">
            <div id="uploadAlert" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
            <form id="uploadProofForm" class="space-y-6">
                
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-700 mb-3">Tugas yang Diselesaikan</h2>
                    <div class="flex gap-4 items-start">
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                            <img id="taskBeforeThumbnail" src="" alt="Tugas" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1">
                            <p id="taskTitleText" class="text-gray-900 font-medium mb-1">-</p>
                            <p id="taskCategoryText" class="text-gray-600 text-sm">-</p>
                            <p id="taskLocationText" class="text-gray-500 text-xs mt-1">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Foto Kondisi Sebelum</h3>
                    <img id="taskBeforeImage" src="" alt="Sebelum" class="w-full h-64 object-cover rounded-lg bg-gray-100" />
                </div>

                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <label class="block text-gray-900 font-medium mb-2 text-sm">
                        Foto Bukti Penyelesaian <span class="text-red-500">*</span>
                    </label>
                    <p class="text-gray-600 mb-4 text-xs">
                        Upload foto kondisi infrastruktur setelah diperbaiki
                    </p>
                    <input type="file" id="photoAfterInput" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4">
                    <img id="photoAfterPreview" src="" alt="Preview" class="hidden w-full h-64 object-cover rounded-lg bg-gray-100" />
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
                    ></textarea>
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
                        onclick="window.location.href='petugas-task-detail.php?id=<?php echo htmlspecialchars($taskId); ?>'"
                        class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 font-semibold"
                    >
                        Batal
                    </button>
                    <button
                        id="submitProofButton"
                        type="submit"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold"
                    >
                        <i class="material-icons text-xl">check_circle</i> Kirim Bukti Penyelesaian
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="js/petugas-auth-guard.js"></script>
    <script>
        PetugasAuth.requireOfficer();
        window.currentTaskId = '<?php echo htmlspecialchars($taskId); ?>';
    </script>
    <script src="js/petugas-upload-proof.js"></script>
</body>
</html>
