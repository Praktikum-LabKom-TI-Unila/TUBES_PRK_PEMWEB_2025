<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Warga</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .max-w-4xl { max-width: 896px; }
        .header-icon {
            color: white; 
            width: 40px; 
            height: 40px; 
            background-color: transparent; 
            border: 1px solid rgba(255,255,255,0.4); 
            border-radius: 9999px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 20px; 
            transition: background-color 0.15s;
        }
        .header-icon:hover {
            background-color: rgba(255,255,255,0.1); 
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen pb-8">
        <div class="bg-blue-600 text-white p-6">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-blue-100 mb-1 text-sm">Selamat Datang,</p>
                        <h1 id="dashboardUserName" class="text-2xl font-semibold">-</h1>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="dashboardLogoutButton" class="header-icon" title="Logout">
                            <i class="material-icons">arrow_forward</i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto p-4">
            <div id="dashboardAlert" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
            <p id="dashboardApiInfo" class="text-xs text-gray-400 mb-4"></p>
            <button onclick="window.location.href='warga-new-complaint.php'" class="w-full bg-blue-600 text-white p-4 rounded-xl shadow-lg hover:bg-blue-700 transition-colors mb-6 flex items-center justify-center gap-3 font-semibold">
                <i class="material-icons text-xl">add</i>
                <span class="text-white">Buat Pengaduan Baru</span>
            </button>

            <div class="grid grid-cols-3 gap-4 mb-6" id="warga-stats-container">
                </div>

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Pengaduan Terbaru</h2>
                <a href="warga-riwayat-pengaduan.php" class="text-blue-600 hover:underline flex items-center gap-1 font-medium text-sm">
                    Lihat Semua
                </a>
            </div>

            <div class="space-y-3" id="recent-complaints-list">
                </div>

            <div id="no-complaints-state" class="hidden bg-white rounded-xl p-12 text-center border border-gray-200 mt-4">
                <i class="material-icons text-6xl text-gray-300 mx-auto mb-4">description</i>
                <p class="text-gray-500">Belum ada pengaduan</p>
            </div>
        </div>
    </div>
    <script src="js/warga-auth.js"></script>
    <script>
        PelaporAuth.requirePelapor();
    </script>
    <script src="js/warga-dashboard.js"></script>
</body>
</html>
