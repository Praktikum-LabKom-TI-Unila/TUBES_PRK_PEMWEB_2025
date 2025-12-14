<?php
$officerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$officerIdAttr = $officerId > 0 ? (string) $officerId : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Petugas Lapangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/admin-auth-guard.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50" data-officer-id="<?php echo htmlspecialchars($officerIdAttr); ?>">
    <main class="py-8">
        <div class="container max-w-5xl mx-auto p-4 space-y-6">
            <header class="flex items-center gap-4">
                <a href="admin-kelola-petugas.php" class="text-xl text-gray-700 hover:text-blue-600 font-semibold">&larr;</a>
                <div class="mr-auto">
                    <h1 class="text-2xl font-semibold text-gray-900">Detail Petugas Lapangan</h1>
                    <p class="text-gray-600 text-sm">Monitor profil dan tugas aktif petugas</p>
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

            <section id="detailSkeleton" class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4 animate-pulse">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gray-200 rounded-full"></div>
                    <div class="space-y-2 flex-1">
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                        <div class="h-4 bg-gray-100 rounded w-1/4"></div>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="h-24 bg-gray-100 rounded-xl"></div>
                    <div class="h-24 bg-gray-100 rounded-xl"></div>
                    <div class="h-24 bg-gray-100 rounded-xl"></div>
                </div>
                <div class="h-40 bg-gray-100 rounded-xl"></div>
            </section>

            <section id="detailShell" class="space-y-6 hidden">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col md:flex-row gap-6">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl">
                            <i class="material-icons">badge</i>
                        </div>
                        <div>
                            <h2 id="officerName" class="text-2xl font-semibold text-gray-900">-</h2>
                            <p id="officerDepartment" class="text-gray-600">-</p>
                            <p id="officerEmployeeId" class="text-sm text-gray-500">ID Pegawai: -</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span id="officerStatusBadge" class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600">Status tidak diketahui</span>
                        <a id="officerEmail" href="mailto:#" class="text-blue-600 text-sm flex items-center gap-2"><i class="material-icons text-base">mail</i><span>-</span></a>
                        <a id="officerPhone" href="tel:#" class="text-blue-600 text-sm flex items-center gap-2"><i class="material-icons text-base">phone</i><span>-</span></a>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4" id="statCards">
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <p class="text-sm text-gray-500">Tugas Aktif</p>
                        <p id="statActiveTasks" class="text-3xl font-semibold text-gray-900">0</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <p class="text-sm text-gray-500">Tugas Selesai</p>
                        <p id="statFinishedTasks" class="text-3xl font-semibold text-gray-900">0</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <p class="text-sm text-gray-500">Total Penugasan</p>
                        <p id="statTotalTasks" class="text-3xl font-semibold text-gray-900">0</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Kontak</h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p class="flex items-center gap-3"><i class="material-icons text-base text-gray-400">home</i><span id="officerAddress">-</span></p>
                            <p class="flex items-center gap-3"><i class="material-icons text-base text-gray-400">map</i><span id="officerSpecialization">-</span></p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 space-y-3">
                        <h3 class="text-lg font-semibold text-gray-900">Meta & Status</h3>
                        <p class="text-sm text-gray-600">Dibuat pada <span id="officerCreatedAt">-</span></p>
                        <p class="text-sm text-gray-600">Terakhir diperbarui <span id="officerUpdatedAt">-</span></p>
                    </div>
                </div>

                <section class="bg-white rounded-2xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Tugas Aktif</h3>
                            <p class="text-sm text-gray-500">Daftar penugasan yang belum selesai</p>
                        </div>
                    </div>
                    <div id="tasksContainer" class="space-y-3">
                        <div class="text-sm text-gray-500">Belum ada tugas aktif.</div>
                    </div>
                </section>
            </section>
        </div>
    </main>

    <script src="js/admin-logout.js"></script>
    <script src="js/admin-api.js"></script>
    <script src="js/admin-detail-petugas.js"></script>
</body>
</html>
