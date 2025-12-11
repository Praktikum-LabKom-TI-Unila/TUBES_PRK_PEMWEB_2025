<?php
$categories = [
    "Jalan Raya",
    "Penerangan Jalan",
    "Drainase & Air",
    "Trotoar",
    "Rambu Lalu Lintas",
    "Taman"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengaduan Baru</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .input-icon-top { position: absolute; left: 12px; top: 12px; color: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto p-4 flex items-center gap-3">
            <a href="warga-dashboard.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
            <h1 class="text-xl font-semibold text-gray-900">Buat Pengaduan Baru</h1>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-4">
        <form id="newComplaintForm" class="space-y-6">
            
            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <label class="block text-gray-900 font-medium mb-2">Foto Bukti <span class="text-red-500">*</span></label>
                <p class="text-gray-600 mb-4 text-sm">Upload foto kondisi infrastruktur yang dilaporkan</p>
                
                <div id="image-preview-area" class="hidden">
                    </div>

                <input type="file" id="photoUpload" name="photo" accept="image/jpeg, image/png" class="hidden" required>
                
                <label for="photoUpload" id="upload-label" class="cursor-pointer border-2 border-dashed border-gray-300 rounded-lg p-10 flex flex-col items-center justify-center text-gray-500 hover:border-blue-500 hover:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="mt-2 text-base font-medium">Klik untuk upload foto</span>
                    <span class="text-sm">JPG, PNG (Max 5MB)</span>
                </label>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <label for="category" class="block text-gray-900 font-medium mb-2">Kategori <span class="text-red-500">*</span></label>
                <select id="category" name="category" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 appearance-none" required>
                    <option value="">Pilih kategori pengaduan</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <label for="description" class="block text-gray-900 font-medium mb-2">Deskripsi Masalah <span class="text-red-500">*</span></label>
                <textarea
                  id="description"
                  name="description"
                  rows="5"
                  placeholder="Jelaskan kondisi infrastruktur yang perlu diperbaiki..."
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none"
                  required
                ></textarea>
                <p class="text-gray-500 mt-2 text-sm">Minimal 20 karakter</p>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <label class="block text-gray-900 font-medium mb-2">Lokasi (Opsional)</label>
                <p class="text-gray-600 mb-4 text-sm">Tandai lokasi pada peta atau masukkan alamat</p>
                
                <input
                  type="text"
                  id="locationInput"
                  placeholder="Masukkan alamat lengkap"
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 mb-4"
                />

                <div id="map-placeholder" class="h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                    <div class="flex flex-col items-center">
                        <i class="material-icons text-4xl text-gray-500">place</i>
                        <p class="mt-2 text-base font-medium">Klik untuk memilih lokasi pada peta</p>
                    </div>
                </div>
                
                <button type="button" id="use-my-location-btn" class="mt-3 flex items-center gap-2 text-blue-600 hover:underline font-medium">
                  <i class="material-icons text-lg">my_location</i> Gunakan Lokasi Saya
                </button>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="window.location.href='warga-dashboard.php'" class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons text-xl">description</i> Kirim Pengaduan
                </button>
            </div>
        </form>
    </div>
    
    <script>
        // --- LOGIKA JAVASCRIPT UNTUK FILE UPLOAD (PREVIEW) ---
        const photoUpload = document.getElementById('photoUpload');
        const uploadLabel = document.getElementById('upload-label');
        const imagePreviewArea = document.getElementById('image-preview-area');

        photoUpload.addEventListener('change', function(event) {
            const file = event.target.files[0];
            
            if (file) {
                // Cek ukuran file (Max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran foto maksimal 5MB.');
                    photoUpload.value = ''; // Reset input file
                    imagePreviewArea.innerHTML = '';
                    uploadLabel.classList.remove('hidden');
                    return;
                }

                const reader = new FileReader();
                
                reader.onload = function(e) {
                    // Sembunyikan label upload
                    uploadLabel.classList.add('hidden');
                    
                    // Tampilkan pratinjau gambar dan tombol hapus
                    imagePreviewArea.classList.remove('hidden');
                    imagePreviewArea.innerHTML = `
                        <div class="relative w-full h-64 border border-gray-300 rounded-lg overflow-hidden mb-4">
                            <img src="${e.target.result}" alt="Foto Bukti" class="w-full h-full object-cover">
                            <button type="button" id="remove-photo-btn" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    `;

                    // Tambahkan event listener untuk tombol hapus
                    document.getElementById('remove-photo-btn').addEventListener('click', function() {
                        photoUpload.value = ''; // Reset input file
                        imagePreviewArea.innerHTML = ''; // Hapus pratinjau
                        uploadLabel.classList.remove('hidden'); // Tampilkan label upload lagi
                        imagePreviewArea.classList.add('hidden');
                    });
                };
                
                reader.readAsDataURL(file); // Membaca file sebagai URL data
            } else {
                // Jika pengguna membatalkan pemilihan file
                imagePreviewArea.innerHTML = '';
                uploadLabel.classList.remove('hidden');
                imagePreviewArea.classList.add('hidden');
            }
        });
        
        // --- LOGIKA JAVASCRIPT UNTUK FORM SUBMIT DAN LOKASI (Opsional) ---
        // (Anda bisa menambahkan logika form submission di sini)
        
    </script>
    <script src="js/warga-new-complaint.js"></script>
</body>
</html>