<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .max-w-4xl { max-width: 896px; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto p-4 flex items-center gap-3">
          <a href="warga-dashboard.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
          <h1 class="text-xl font-semibold text-gray-900">Riwayat Pengaduan</h1>
        </div>
    </div>

    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-white rounded-xl p-4 border border-gray-200 mb-4">
          <div class="flex items-center gap-2 mb-3">
            <i class="material-icons text-xl text-gray-600">filter_list</i>
            <p class="text-lg font-medium text-gray-900">Filter Status</p>
          </div>
          
          <div class="flex flex-wrap gap-2" id="filter-buttons-container">
            </div>
        </div>

        <div class="grid grid-cols-4 gap-3 mb-4" id="history-stats-container">
          </div>

        <div class="space-y-3" id="complaints-history-list">
          </div>

        <div id="no-filtered-complaints" class="hidden bg-white rounded-xl p-12 text-center border border-gray-200 mt-3">
          <p class="text-gray-500">Tidak ada pengaduan dengan status ini</p>
        </div>
    </div>

    <script src="js/warga-riwayat-pengaduan.js"></script>
</body>
</html>