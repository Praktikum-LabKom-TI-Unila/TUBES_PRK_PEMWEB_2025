<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="js/admin-auth-guard.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* CSS tambahan minimal */
        .max-w-6xl { max-width: 1152px; }
    </style>
</head>
<body class="bg-gray-50">
    <main class="content-wrapper py-8">
        <div class="container max-w-6xl mx-auto p-4">
            
            <header class="page-header flex items-center gap-4 mb-6">
                <a href="admin-dashboard.php" class="text-xl text-gray-700 hover:text-blue-600 font-semibold">&larr;</a>
                <div class="mr-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Kelola Petugas</h1>
                    <p class="text-gray-600 text-sm">Manajemen data petugas lapangan</p>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-lg border border-red-100 text-sm font-semibold hover:bg-red-100"
                    data-admin-logout
                    data-default-text="Logout"
                    data-loading-text="Keluar..."
                >
                    <i class="material-icons text-base">logout</i>
                    <span>Logout</span>
                </button>
            </header>
            <div id="pageAlert" class="hidden"></div>
            
            <section class="grid md:grid-cols-3 gap-4 mb-6" id="ringkasan-petugas-container">
                </section>

            <section class="bg-white rounded-xl shadow-sm p-6 border border-gray-200 mb-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">search</i>
                        <input
                            type="text"
                            id="searchPetugas"
                            placeholder="Cari nama, departemen, spesialisasi, atau ID..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
                        />
                    </div>

                    <select
                        id="statusFilter"
                        class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 appearance-none"
                    >
                        <option value="all">Semua Status</option>
                        <option value="tersedia">Tersedia</option>
                        <option value="sibuk">Sedang Bertugas</option>
                    </select>
                </div>

                <p class="text-gray-600 mt-4" id="showing-info">Menampilkan 5 dari 5 petugas</p>
            </section>

            <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div id="tableSkeleton" class="p-6 space-y-4 hidden">
                    <div class="animate-pulse space-y-4">
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-4 bg-gray-100 rounded"></div>
                        <div class="h-4 bg-gray-100 rounded"></div>
                        <div class="h-4 bg-gray-100 rounded"></div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-gray-700 font-semibold text-sm uppercase">Petugas</th>
                                <th class="px-6 py-4 text-left text-gray-700 font-semibold text-sm uppercase">Departemen</th>
                                <th class="px-6 py-4 text-left text-gray-700 font-semibold text-sm uppercase">Spesialisasi</th>
                                <th class="px-6 py-4 text-left text-gray-700 font-semibold text-sm uppercase">Tugas Aktif</th>
                                <th class="px-6 py-4 text-left text-gray-700 font-semibold text-sm uppercase">Status</th>
                                <th class="px-6 py-4 text-center text-gray-700 font-semibold text-sm uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-body-petugas" class="divide-y divide-gray-100">
                            </tbody>
                    </table>
                </div>
            </section>

            <div class="flex items-center justify-between mt-4" id="paginationControls">
                <button id="prevPageBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Sebelumnya</button>
                <span id="paginationInfo" class="text-sm text-gray-500">Halaman 1 dari 1</span>
                <button id="nextPageBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Berikutnya</button>
            </div>
            
            <div id="empty-state" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center mt-4">
                <i class="material-icons text-6xl text-gray-300 mx-auto mb-4">group</i>
                <p class="text-gray-500">Tidak ditemukan petugas yang sesuai dengan pencarian atau filter.</p>
            </div>
            
        </div>
    </main>
        <div id="detail-modal-container"></div>
    <script src="js/admin-logout.js"></script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-kelola-petugas.js"></script>
</body>
</html>