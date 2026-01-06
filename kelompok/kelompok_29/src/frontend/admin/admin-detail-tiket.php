<?php
$ticketId = isset($_GET['id']) ? trim($_GET['id']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="js/admin-auth-guard.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        .header-sticky { background-color: white; border-bottom: 1px solid #e9ecef; position: sticky; top: 0; z-index: 10; }
        .map-placeholder { background-color: #f0f0f0; }
        .officer-avatar { width: 48px; height: 48px; background-color: #e3f2fd; border-radius: 9999px; display: flex; align-items: center; justify-content: center; color: #1976d2; font-size: 24px; flex-shrink: 0; }
        /* Timeline */
        .timeline-wrapper { position: relative; padding-left: 10px; }
        .timeline-point { width: 12px; height: 12px; border-radius: 50%; border: 2px solid white; position: absolute; left: 0; transform: translateX(-50%); z-index: 5; }
    </style>
</head>
<body class="bg-gray-50" data-ticket-id="<?php echo htmlspecialchars($ticketId); ?>">
    
    <div class="header-sticky">
        <div class="max-w-5xl mx-auto p-4 flex items-center gap-3">
            <a href="admin-manajemen-tiket.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">&larr;</a>
            <div class="flex-grow">
                <h1 id="ticketHeaderTitle" class="text-2xl font-semibold text-gray-900">Detail Tiket</h1>
                <p id="ticketHeaderReporter" class="text-gray-600 text-sm">Pelapor tidak diketahui</p>
            </div>
            <div class="flex items-center gap-3">
                <div id="statusBadgeContainer" class="text-sm text-gray-500">Status tidak tersedia</div>
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
            </div>
        </div>
    </div>

    <section id="pageAlert" class="hidden max-w-5xl mx-auto mt-4 px-4"></section>

    <main class="content-wrapper">
        <div class="max-w-5xl mx-auto p-4 grid lg:grid-cols-3 gap-4" id="detailShell">
            
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-xl overflow-hidden border border-gray-200" id="ticketImageWrapper">
                    <img id="ticketImage" src="https://placehold.co/1200x600?text=Memuat+foto" alt="Foto Pengaduan" class="w-full h-80 object-cover">
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h2 id="ticketTitle" class="mb-4 text-2xl font-semibold text-gray-900">Memuat judul...</h2>
                    <div class="flex flex-wrap gap-4 text-gray-600 mb-6 text-sm" id="ticketMeta">
                        <div class="flex items-center gap-2">
                            <i class="material-icons text-base">calendar_today</i>
                            <span id="ticketCreatedAt">-</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="material-icons text-base">description</i>
                            <span id="ticketCategory">-</span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-700 mb-2 font-medium">Deskripsi:</p>
                        <p id="ticketDescription" class="text-gray-600">Memuat deskripsi...</p>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex items-start gap-2">
                            <i class="material-icons text-xl text-gray-400 mt-0.5">location_on</i>
                            <div>
                                <p class="text-gray-700 mb-1 font-medium">Lokasi:</p>
                                <p id="ticketLocation" class="text-gray-600">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Peta Lokasi</h3>
                    <div id="ticketMap" class="h-56 rounded-xl border border-gray-200 overflow-hidden"></div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-200" id="reporterCard">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Informasi Pelapor</h3>
                    <div class="space-y-2 text-gray-700">
                        <p><span class="font-semibold">Nama:</span> <span id="reporterName">-</span></p>
                        <p><span class="font-semibold">Email:</span> <span id="reporterEmail">-</span></p>
                        <p><span class="font-semibold">Telepon:</span> <span id="reporterPhone">-</span></p>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-6 border border-gray-200 hidden" id="proofSection">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-gray-900">Bukti Penyelesaian</h3>
                        <span class="text-sm text-gray-500" id="proofMeta"></span>
                    </div>
                    <div id="proofList" class="space-y-4"></div>
                </div>
                </div>

            <div class="space-y-4">
                
                <div class="bg-white rounded-xl p-6 border border-gray-200" id="actionPanel">
                    <h3 class="mb-4 text-xl font-semibold">Aksi</h3>
                    <div class="space-y-3" id="actionButtonsContainer">
                        <p class="text-sm text-gray-500">Memuat aksi yang tersedia...</p>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 hidden" id="assignedOfficerCard">
                    <h3 class="mb-4 text-xl font-semibold">Petugas Ditugaskan</h3>
                    <div class="flex items-start gap-3">
                        <div class="officer-avatar"><i class="material-icons">person</i></div>
                        <div>
                            <p id="officerName" class="text-gray-900 font-semibold mb-1">-</p>
                            <p id="officerMeta" class="text-gray-600 text-sm">-</p>
                            <p id="officerStatus" class="text-gray-500 text-xs mt-1">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200" id="timelineCard">
                    <h3 class="mb-4 text-xl font-semibold">Timeline</h3>
                    <div id="timelineContainer" class="space-y-4">
                        <p class="text-sm text-gray-500">Memuat timeline...</p>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <div id="detailSkeleton" class="max-w-5xl mx-auto p-6 hidden">
        <div class="animate-pulse space-y-4">
            <div class="h-6 bg-gray-200 rounded"></div>
            <div class="h-64 bg-gray-200 rounded"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="h-32 bg-gray-200 rounded"></div>
                <div class="h-32 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <div id="modalRoot"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="js/admin-logout.js"></script>
    <script src="js/admin-detail-tiket.js"></script>
    <script>
        window.__ADMIN_TICKET_ID__ = '<?php echo htmlspecialchars($ticketId); ?>';
    </script>
</body>
</html>
