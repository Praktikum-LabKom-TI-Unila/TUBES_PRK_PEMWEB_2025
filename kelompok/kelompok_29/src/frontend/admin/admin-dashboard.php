<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    
    <header class="bg-blue-600 text-white p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 mb-1 text-sm">Dashboard Admin</p>
                    <h1 class="text-2xl font-semibold">Manajemen Pengaduan</h1>
                </div>
                <div class="flex gap-2">
                    <a href="admin-edit-profil.php" class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-400 text-xl" title="Edit Profile">
                        <i class="material-icons">person</i>
                    </a>
                    <a href="admin-login.php" class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-400 text-xl" title="Logout">
                        <i class="material-icons">logout</i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="bg-gray-50 pb-8">
        <div class="max-w-6xl mx-auto p-4">
            
            <div class="grid md:grid-cols-4 gap-4 mb-6" id="stats-cards-container">
                </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 mb-6">
                <h2 class="text-xl font-semibold mb-4">Menu Utama</h2>
                <div class="grid md:grid-cols-3 gap-4" id="menu-utama-container">
                    </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Aktivitas Terbaru</h2>
                <div class="space-y-4" id="aktivitas-terbaru-container">
                    </div>
            </div>
        </div>
    </main>
    
    <script src="js/admin-dashboard.js"></script>
</body>
</html>