<?php 
// Koneksi database
require_once '../../../config/database.php';

$assetPath = "../../assets/";

// Ambil parameter pencarian dari URL
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// Query untuk mengambil data tutor
$query = "SELECT t.id, t.nama_lengkap as nama, t.email, t.keahlian, t.harga_per_sesi, t.rating 
          FROM tutor t 
          WHERE t.status = 'Aktif'";

// Jika ada pencarian, tambahkan filter
if (!empty($searchQuery)) {
    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);
    $query .= " AND (t.nama_lengkap LIKE '%{$searchQuery}%' 
                 OR t.keahlian LIKE '%{$searchQuery}%')";
}

$query .= " ORDER BY t.rating DESC";

$result = mysqli_query($conn, $query);

$tutorsData = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Ambil mata pelajaran pertama dari tutor_mapel
        $mapelQuery = "SELECT nama_mapel FROM tutor_mapel WHERE tutor_id = {$row['id']} LIMIT 1";
        $mapelResult = mysqli_query($conn, $mapelQuery);
        $mapelRow = mysqli_fetch_assoc($mapelResult);
        
        $tutorsData[] = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'mapel' => $mapelRow['nama_mapel'] ?? $row['keahlian'],
            'harga' => $row['harga_per_sesi'] ?? 100000,
            'rating' => $row['rating'] ?? 4.5
        ];
    }
}

// Jika database kosong, gunakan data dummy
if (empty($tutorsData)) {
    $tutorsData = [
        ['id' => 1, 'nama' => 'Rizky Ramadhan', 'mapel' => 'Matematika', 'harga' => 350000, 'rating' => 4.9],
        ['id' => 2, 'nama' => 'Aulia Putri', 'mapel' => 'Bahasa Inggris', 'harga' => 420000, 'rating' => 5.0],
        ['id' => 3, 'nama' => 'Dimas Wahyu', 'mapel' => 'Fisika', 'harga' => 300000, 'rating' => 4.7],
        ['id' => 4, 'nama' => 'Nadia Fitri', 'mapel' => 'Kimia', 'harga' => 400000, 'rating' => 4.8],
        ['id' => 5, 'nama' => 'Farhan Akbar', 'mapel' => 'Biologi', 'harga' => 320000, 'rating' => 4.6],
        ['id' => 6, 'nama' => 'Sinta Maharani', 'mapel' => 'Bahasa Indonesia', 'harga' => 280000, 'rating' => 4.5],
        ['id' => 7, 'nama' => 'Adi Pratama', 'mapel' => 'Ekonomi', 'harga' => 330000, 'rating' => 4.7],
        ['id' => 8, 'nama' => 'Maya Sari', 'mapel' => 'Sejarah', 'harga' => 260000, 'rating' => 4.4]
    ];
}

include '../../layouts/header.php'; 
?>

<main class="container" style="padding: 120px 120px 60px;">

  <!-- TITLE -->
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h2 style="font-size: 32px; font-weight: 700; color: #0C4A60;">Hasil Pencarian Tutor</h2>

    <a href="landing_page.php" style="color: #64748b; font-weight: 600; text-decoration: none;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- SEARCH BOX -->
  <div class="search-box" style="margin-top:20px;">
    <form method="GET" action="search_result.php">
      <input type="text" id="searchInput" name="q" placeholder="Cari tutor atau mata pelajaran..." value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button id="btnSearch" type="submit" class="btn-search">Cari</button>
    </form>
  </div>

  <!-- SEARCH RESULT GRID -->
  <div id="resultContainer" class="result-grid fade-up" style="margin-top:30px;">
    <!-- JS render -->
  </div>

</main>

<?php include '../../layouts/footer.php'; ?>


<!-- ================= JS: DATA TUTOR + SEARCH ================= -->
<script>
const tutorsData = <?php echo json_encode($tutorsData); ?>;

// format ribuan
function rp(n){
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// RENDER RESULT
function renderResults(list){
  const container = document.getElementById('resultContainer');
  container.innerHTML = "";

  if(list.length === 0){
    container.innerHTML = `
      <p style="grid-column:1/-1;text-align:center;color:#64748b;">Tidak ada tutor ditemukan…</p>
    `;
    return;
  }

  list.forEach(t => {
    const card = document.createElement("div");
    card.className = "result-card";

    card.innerHTML = `
      <div class="result-photo">${t.nama.charAt(0)}</div>
      <div class="result-name">${t.nama}</div>
      <div class="result-subject">${t.mapel}</div>
      <div class="result-price">Rp ${rp(t.harga)}</div>

      <a href="detail_tutor.php?id=${t.id}" 
         class="result-btn">
        Lihat Detail
      </a>
    `;

    container.appendChild(card);
  });
}

// INITIAL RENDER
renderResults(tutorsData);

// SEARCH EVENT - live search
document.getElementById("searchInput").addEventListener("input", e => {
  const q = e.target.value.toLowerCase().trim();
  const filtered = tutorsData.filter(t =>
    t.nama.toLowerCase().includes(q) || t.mapel.toLowerCase().includes(q)
  );
  renderResults(filtered);
});
</script>
