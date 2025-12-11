<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas Lapangan</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .max-w-4xl { max-width: 896px; }
    </style>
</head>
<body class="bg-gray-50">
    
    <header class="bg-blue-600 text-white p-6">
        <div class="max-w-4xl mx-auto">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 mb-1 text-sm">Selamat Datang,</p>
              <h1 id="dashboardName" class="text-2xl font-semibold">-</h1>
              <p id="dashboardDepartment" class="text-blue-100 text-sm">Petugas Lapangan</p>
            </div>
            <div class="flex gap-2">
              <a href="petugas-edit-profil.php" class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-400 text-xl transition-colors" title="Edit Profil">
                <i class="material-icons">person</i>
              </a>
              <button type="button" id="logoutButton" class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-400 text-xl transition-colors" title="Logout">
                <i class="material-icons">logout</i>
              </button>
            </div>
          </div>
        </div>
    </header>

    <main class="bg-gray-50 pb-8">
        <div class="max-w-4xl mx-auto p-4">
            <div id="dashboardAlert" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
            
            <div id="statsLoader" class="mb-4 bg-white p-4 rounded-xl border border-gray-200 text-center text-gray-500">Memuat statistik...</div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" id="stats-cards-container"></div>

            <div class="mb-6">
              <h2 class="text-xl font-semibold mb-4 text-gray-800">Tugas Aktif</h2>
              <div id="activeTasksLoader" class="bg-white rounded-xl p-4 border border-gray-200 text-center text-gray-500">Memuat tugas aktif...</div>
              <div class="space-y-3" id="active-tasks-list"></div>
              <div id="no-active-tasks" class="hidden bg-white rounded-xl p-12 text-center border border-gray-200 mt-3">
                <p class="text-gray-500">Tidak ada tugas aktif saat ini</p>
              </div>
              <div id="activePagination" class="hidden mt-3 flex items-center justify-between text-sm"></div>
            </div>

            <div>
              <h2 class="text-xl font-semibold mb-4 text-gray-800">Tugas Selesai</h2>
              <div id="completedTasksLoader" class="bg-white rounded-xl p-4 border border-gray-200 text-center text-gray-500">Memuat riwayat tugas...</div>
              <div class="space-y-3" id="completed-tasks-list"></div>
              <div id="no-completed-tasks" class="hidden bg-white rounded-xl p-8 text-center border border-gray-200 mt-3">
                <p class="text-gray-500">Belum ada tugas yang diselesaikan</p>
              </div>
              <div id="completedPagination" class="hidden mt-3 flex items-center justify-between text-sm"></div>
            </div>
        </div>
    </main>
    
    <script src="js/petugas-auth-guard.js"></script>
    <script>
        PetugasAuth.requireOfficer();
    </script>
    <script src="js/petugas-task-list.js"></script>
</body>
</html>
