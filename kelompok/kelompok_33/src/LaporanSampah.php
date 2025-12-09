<?php
require 'config.php';

// Ambil semua laporan dari DB
$query = "
SELECT l.*, 
       (SELECT nama_file FROM foto_laporan WHERE laporan_id = l.id LIMIT 1) AS foto
FROM laporan l
ORDER BY l.id DESC
";
$laporan = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CleanSpot - Laporan Sampah</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

  <!-- Navbar -->
  <nav class="bg-white shadow p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-green-600">CleanSpot</h1>
  </nav>

  <div class="container mx-auto p-6">
    <h2 class="text-3xl font-semibold mb-4 text-gray-700">Laporan Sampah</h2>

    <!-- Form Laporan -->
    <div class="bg-white p-6 rounded-xl shadow mb-10">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">Buat Laporan Baru</h3>

      <form action="proses_laporan.php" method="POST" enctype="multipart/form-data"
        class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>
          <label class="block text-gray-700 font-medium mb-2">Judul Laporan</label>
          <input type="text" name="judul" required
            class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Kategori</label>
          <select name="kategori" class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400">
            <option value="organik">Organik</option>
            <option value="non-organik">Non-Organik</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
          <textarea name="deskripsi" rows="3"
            class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400"></textarea>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Upload Foto</label>
          <input type="file" name="foto" accept="image/*" class="w-full" required />
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Alamat Lokasi</label>
          <input type="text" name="alamat"
            class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Latitude</label>
          <input type="text" name="lat"
            class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400" />
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Longitude</label>
          <input type="text" name="lng"
            class="w-full rounded-lg border p-3 focus:ring-2 focus:ring-green-400" />
        </div>

        <div class="md:col-span-2 flex justify-end">
          <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
            Kirim Laporan
          </button>
        </div>

      </form>
    </div>

    <!-- TABEL LAPORAN -->
    <div class="bg-white p-6 rounded-xl shadow">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">Daftar Laporan</h3>

      <table class="min-w-full table-auto">
        <thead>
          <tr class="bg-green-100 text-gray-700">
            <th class="p-3 text-left">Foto</th>
            <th class="p-3 text-left">Judul</th>
            <th class="p-3 text-left">Kategori</th>
            <th class="p-3 text-left">Lokasi</th>
            <th class="p-3 text-left">Status</th>
          </tr>
        </thead>

        <tbody>
          <?php while ($row = mysqli_fetch_assoc($laporan)) { ?>
          <tr class="border-b">
            <td class="p-3">
              <?php if ($row['foto']) { ?>
              <img src="uploads/<?= $row['foto'] ?>" class="h-16 rounded">
              <?php } else { echo "-"; } ?>
            </td>

            <td class="p-3"><?= htmlspecialchars($row['judul']) ?></td>
            <td class="p-3"><?= $row['kategori'] ?></td>
            <td class="p-3"><?= $row['alamat'] ?></td>
            <td class="p-3 font-medium text-yellow-600"><?= $row['status'] ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>

  </div>
</body>
</html>
