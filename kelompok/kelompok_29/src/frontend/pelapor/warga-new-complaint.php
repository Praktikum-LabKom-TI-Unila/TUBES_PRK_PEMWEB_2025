<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pengaduan Baru</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
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
        <form id="newComplaintForm" class="space-y-6" enctype="multipart/form-data" novalidate>
            <div id="newComplaintAlert" class="hidden p-3 rounded-lg text-sm"></div>
            
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
        <select id="categorySelect" name="category" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 appearance-none" required>
            <option value="">Memuat kategori...</option>
        </select>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <label for="titleInput" class="block text-gray-900 font-medium mb-2">Judul Pengaduan <span class="text-red-500">*</span></label>
                <input id="titleInput" type="text" name="title" placeholder="Contoh: Jalan Berlubang di Jl. Sudirman" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
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

            <div class="bg-white rounded-xl p-6 border border-gray-200 space-y-4">
                <div>
                    <label class="block text-gray-900 font-medium mb-2">Alamat / Lokasi</label>
                    <textarea id="locationInput" placeholder="Masukkan alamat lengkap" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none" rows="2"></textarea>
                </div>
                <div class="grid md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                        <input id="latitudeInput" type="text" readonly class="w-full p-2 border border-gray-200 rounded-lg bg-gray-50 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                        <input id="longitudeInput" type="text" readonly class="w-full p-2 border border-gray-200 rounded-lg bg-gray-50 text-sm">
                    </div>
                </div>

                <div id="complaintMap" class="h-64 bg-gray-200 rounded-lg"></div>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="material-icons text-base">info</i>
                    Klik pada peta untuk memilih koordinat. Sistem akan mencoba mengisi alamat otomatis.
                </div>
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
    
    <script src="js/warga-auth.js"></script>
    <script>
        PelaporAuth.requirePelapor();
    </script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="js/warga-new-complaint.js"></script>
</body>
</html>
