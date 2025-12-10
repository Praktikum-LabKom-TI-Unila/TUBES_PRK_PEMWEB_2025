<?php 
$assetPath = "../../assets/";
include '../../layouts/header.php'; 
?>

<main class="site-main" style="max-width:1200px;margin:auto;padding:20px;">

  <!-- TITLE -->
  <div class="d-flex justify-content-between align-items-center mt-4">
    <h2 class="search-result-title">Hasil Pencarian Tutor</h2>

    <a href="landing_page.php" class="text-muted" style="font-weight:600;">
      <i class="bi bi-arrow-left"></i> Kembali
    </a>
  </div>

  <!-- SEARCH BOX -->
  <div class="search-box" style="margin-top:20px;">
    <input type="text" id="searchInput" placeholder="Cari tutor atau mata pelajaran...">
    <button id="btnSearch" class="btn-search">Cari</button>
  </div>

  <!-- SEARCH RESULT GRID -->
  <div id="resultContainer" class="result-grid fade-up" style="margin-top:30px;">
    <!-- JS render -->
  </div>

</main>

<?php include '../../layouts/footer.php'; ?>


<!-- ================= JS: DATA TUTOR + SEARCH ================= -->
<script>
const tutorsData = [
  { nama: "Rizky Ramadhan", mapel: "Matematika", harga: 350000, rating: 4.9 },
  { nama: "Aulia Putri", mapel: "Bahasa Inggris", harga: 420000, rating: 5.0 },
  { nama: "Dimas Wahyu", mapel: "Fisika", harga: 300000, rating: 4.7 },
  { nama: "Nadia Fitri", mapel: "Kimia", harga: 400000, rating: 4.8 },
  { nama: "Farhan Akbar", mapel: "Biologi", harga: 320000, rating: 4.6 },
  { nama: "Sinta Maharani", mapel: "Bahasa Indonesia", harga: 280000, rating: 4.5 },
  { nama: "Adi Pratama", mapel: "Ekonomi", harga: 330000, rating: 4.7 },
  { nama: "Maya Sari", mapel: "Sejarah", harga: 260000, rating: 4.4 }
];

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

      <a href="detail_tutor.php?nama=${encodeURIComponent(t.nama)}&mapel=${encodeURIComponent(t.mapel)}&harga=${t.harga}&rating=${t.rating}" 
         class="result-btn">
        Lihat Detail
      </a>
    `;

    container.appendChild(card);
  });
}

// INITIAL RENDER
renderResults(tutorsData);

// SEARCH EVENT
document.getElementById("searchInput").addEventListener("input", e => {
  const q = e.target.value.toLowerCase().trim();
  const filtered = tutorsData.filter(t =>
    t.nama.toLowerCase().includes(q) || t.mapel.toLowerCase().includes(q)
  );
  renderResults(filtered);
});

// Button search
document.getElementById("btnSearch").addEventListener("click", () => {
  const q = document.getElementById("searchInput").value.toLowerCase().trim();
  const filtered = tutorsData.filter(t =>
    t.nama.toLowerCase().includes(q) || t.mapel.toLowerCase().includes(q)
  );
  renderResults(filtered);
});
</script>
