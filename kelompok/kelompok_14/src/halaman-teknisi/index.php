<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Teknisi - FixTrack</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
  <!-- Header -->
  <header class="sticky top-0 z-40 bg-white shadow border-b">
    <div class="px-6 py-4 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <div class="bg-blue-600 text-white p-2 rounded-lg"><i class="fas fa-tools"></i></div>
        <h1 class="text-xl font-bold">FixTrack <span class="text-blue-600">Teknisi</span></h1>
      </div>
      <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">Halo, Teknisi</span>
        <a href="#" class="text-blue-600 text-sm font-medium"><i class="fas fa-user mr-1"></i>Profile</a>
        <a href="#" class="text-red-600 text-sm font-medium"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
      </div>
    </div>
  </header>

  <!-- Main -->
  <main class="p-6 space-y-6">
    <div>
      <h2 class="text-3xl font-bold">Dashboard</h2>
      <p class="text-gray-500 text-sm">Ringkasan aktivitas bengkel hari ini</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl p-5 shadow border"><div class="flex justify-between items-start"><div><p class="text-gray-500 text-sm mb-2">Antrian Baru</p><p class="text-3xl font-bold">0</p><p class="text-xs text-gray-400 mt-1">Perlu Diproses</p></div><div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center"><i class="fas fa-clipboard text-blue-600 text-xl"></i></div></div></div>
      <div class="bg-white rounded-xl p-5 shadow border"><div class="flex justify-between items-start"><div><p class="text-gray-500 text-sm mb-2">Proses</p><p class="text-3xl font-bold">0</p><p class="text-xs text-gray-400 mt-1">Sedang Dikerjakan</p></div><div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center"><i class="fas fa-cog text-yellow-600 text-xl"></i></div></div></div>
      <div class="bg-white rounded-xl p-5 shadow border"><div class="flex justify-between items-start"><div><p class="text-gray-500 text-sm mb-2">Selesai</p><p class="text-3xl font-bold">0</p><p class="text-xs text-gray-400 mt-1">Siap Diambil</p></div><div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center"><i class="fas fa-check text-green-600 text-xl"></i></div></div></div>
      <div class="bg-white rounded-xl p-5 shadow border"><div class="flex justify-between items-start"><div><p class="text-gray-500 text-sm mb-2">Omset</p><p class="text-3xl font-bold">Rp 0</p><p class="text-xs text-gray-400 mt-1">Bulan Ini</p></div><div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center"><i class="fas fa-wallet text-purple-600 text-xl"></i></div></div></div>
    </div>

    <!-- Services Table -->
    <div class="bg-white rounded-xl shadow border">
      <div class="px-6 py-4 border-b flex justify-between items-center"><h3 class="text-xl font-bold">Servis Terbaru</h3><a href="#" class="text-blue-600 text-sm font-medium">Lihat Semua</a></div>
      <div class="px-6 py-4 border-b flex flex-wrap gap-3 justify-between items-center"><div class="flex flex-wrap gap-3 flex-1"><input type="text" id="searchInput" placeholder="Cari nama pelanggan..." class="px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none text-sm" /><select id="statusFilter" class="px-4 py-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none text-sm"><option value="">Semua Status</option><option value="1">Diterima admin</option><option value="2">Dikerjakan oleh teknisi</option><option value="3">Selesai dikerjakan</option><option value="4">Barang sudah dapat diambil</option></select><button onclick="filterData()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">Cari</button></div><button type="button" onclick="openAddServiceModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg text-sm font-medium whitespace-nowrap">+ Tambah</button></div>
      <div class="overflow-x-auto"><table class="w-full"><thead class="bg-gray-50 border-b"><tr><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">NO. RESI</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th><th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th></tr></thead><tbody class="divide-y" id="serviceTableBody"></tbody></table><div id="emptyState" class="px-6 py-12 text-center"><p class="text-gray-400 text-sm">Belum ada data servis.</p></div></div>
    </div>

    <!-- Modals -->
    <div id="addServiceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"><div class="bg-white rounded-xl shadow-2xl w-full max-w-md"><div class="px-6 py-4 border-b flex justify-between"><h2 class="text-xl font-bold">Tambah Service Baru</h2><button type="button" onclick="closeAddServiceModal()" class="text-gray-400 text-2xl">×</button></div><div class="p-6 space-y-4"><div><label class="block text-sm font-medium mb-2">Nama Pelanggan</label><input type="text" id="newCustomerName" placeholder="Masukkan nama pelanggan..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" /></div><div><label class="block text-sm font-medium mb-2">Nama Barang</label><input type="text" id="newItemName" placeholder="Masukkan nama barang..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" /></div><div><label class="block text-sm font-medium mb-2">Status Awal</label><select id="newStatus" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm"><option value="1">Diterima admin</option><option value="2">Dikerjakan oleh teknisi</option><option value="3">Selesai dikerjakan</option><option value="4">Barang sudah dapat diambil</option></select></div><div class="flex gap-3"><button type="button" onclick="addNewService()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">Tambah</button><button type="button" onclick="closeAddServiceModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg font-medium">Batal</button></div></div></div></div>

    <div id="diagnosaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"><div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-screen overflow-y-auto"><div class="px-6 py-4 border-b flex justify-between sticky top-0 bg-white"><h2 class="text-xl font-bold">Diagnosa: <span id="diagnosaCustomerName"></span> (<span id="diagnosaItemName"></span>)</h2><button type="button" onclick="closeDiagnosaModal()" class="text-gray-400 text-2xl">×</button></div><div class="p-6 space-y-6"><div><h3 class="text-lg font-bold mb-4">Deskripsi Diagnosa</h3><label class="block text-sm font-medium mb-2">Penjelasan Diagnosa Akhir</label><textarea id="diagnosisDesc" placeholder="Masukkan penjelasan diagnosa..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none h-24 resize-none text-sm"></textarea><label class="block text-sm font-medium mb-2 mt-3">Catatan Teknisi</label><textarea id="additionalDetails" placeholder="Detail tambahan atau catatan penting..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none h-20 resize-none text-sm"></textarea></div><div><h3 class="text-lg font-bold mb-4">Komponen & Biaya Jasa</h3><div id="componentList" class="space-y-3"></div><button type="button" onclick="addComponentField()" class="text-sm font-medium text-blue-600 hover:text-blue-700 mt-3">+ Tambah Komponen</button><label class="block text-sm font-medium mb-2 mt-4">Harga Jasa (Rp)</label><input type="text" id="laborCost" placeholder="Masukkan harga jasa..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm" oninput="handleCostInput(event); calculateTotal()" /></div><div class="bg-blue-600 p-4 rounded-lg text-white flex justify-between items-center"><div><label class="block text-sm font-medium mb-1">Total Biaya</label><p class="text-3xl font-bold" id="totalDisplay">Rp 0</p></div><button type="button" onclick="showStruk()" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium text-sm">Lihat Struk</button></div><div class="flex gap-3"><button type="button" onclick="saveDetails()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">Simpan & Perbarui</button><button type="button" onclick="closeDiagnosaModal()" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-lg font-medium">Tutup</button></div></div></div></div>

    <div id="strukModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"><div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-screen overflow-y-auto"><div class="px-6 py-4 border-b flex justify-between sticky top-0 bg-white"><h2 class="text-xl font-bold">Struk Biaya Service</h2><button type="button" onclick="closeStrukModal()" class="text-gray-400 text-2xl">×</button></div><div class="p-6 space-y-4" id="strukContent"></div><div class="px-6 py-4 border-t"><button type="button" onclick="printStruk()" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Cetak Struk</button></div></div></div>
  </main>

  <script src="js/app.js"></script>
</body>
</html>
