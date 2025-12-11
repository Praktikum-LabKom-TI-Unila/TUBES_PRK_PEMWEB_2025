<?php 
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../../config/database.php';

$assetPath = "../../assets/";

$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : '';
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : (isset($_SESSION['email']) ? $_SESSION['email'] : '');

$siswa_data = null;
if ($isLoggedIn && $userRole == 'learner') {
    $siswa_query = "SELECT * FROM siswa WHERE email = '$userEmail' LIMIT 1";
    $siswa_result = mysqli_query($conn, $siswa_query);
    $siswa_data = mysqli_fetch_assoc($siswa_result);
}

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
        
        $mapel = $row['keahlian']; // default
        if ($mapelResult && mysqli_num_rows($mapelResult) > 0) {
            $mapelRow = mysqli_fetch_assoc($mapelResult);
            $mapel = $mapelRow['nama_mapel'];
        }
        
        $tutorsData[] = [
            'id' => (int)$row['id'],
            'nama' => $row['nama'],
            'mapel' => $mapel,
            'harga' => (int)($row['harga_per_sesi'] ?? 100000),
            'rating' => (float)($row['rating'] ?? 4.5)
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
?>

<?php if ($isLoggedIn && $userRole == 'learner' && $siswa_data): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Tutor - ScholarBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $assetPath; ?>css/style.css">
</head>
<body>

<!-- NAVBAR LEARNER -->
<nav class="sb-navbar">
    <div class="sb-nav-container">
        <div class="sb-brand">
            <img src="<?php echo $assetPath; ?>img/logo.png" alt="ScholarBridge Logo" class="logo">
            <span>ScholarBridge</span>
        </div>
        <ul class="sb-menu">
            <li><a href="../learner/dashboard_siswa.php">Beranda</a></li>
            <li><a href="search_result.php" class="active">Cari Tutor</a></li>
            <li><a href="../learner/sesi_saya.php">Sesi Saya</a></li>
            <li><a href="#testimoni">Testimoni</a></li>
        </ul>
        <div style="display: flex; gap: 10px; align-items: center;">
            <div style="position: relative;">
                <button onclick="toggleDropdown()" class="sb-daftar" style="display: flex; align-items: center; gap: 8px; cursor: pointer; border: none; background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($siswa_data['nama_lengkap']); ?>
                </button>
                <div id="userDropdown" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 8px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000;">
                    <div style="padding: 12px 16px; border-bottom: 1px solid #eee;">
                        <p style="margin: 0; font-weight: 600; color: #333;"><?php echo htmlspecialchars($siswa_data['nama_lengkap']); ?></p>
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;"><?php echo $siswa_data['jenjang'] . ' - ' . $siswa_data['kelas']; ?></p>
                    </div>
                    <a href="../learner/profil.php" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                        <i class="bi bi-person"></i> Profil Saya
                    </a>
                    <a href="../learner/sesi_saya.php" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                        <i class="bi bi-calendar-check"></i> Sesi Belajar
                    </a>
                    <a href="../../../backend/auth/logout.php" style="display: block; padding: 12px 16px; color: #dc3545; text-decoration: none;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.sb-navbar { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
.sb-nav-container { max-width: 1200px; margin: 0 auto; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
.sb-brand { display: flex; align-items: center; gap: 10px; font-size: 24px; font-weight: 700; color: #0C4A60; }
.sb-brand .logo { height: 40px; width: auto; }
.sb-menu { list-style: none; display: flex; gap: 30px; margin: 0; padding: 0; }
.sb-menu a { text-decoration: none; color: #333; font-weight: 500; transition: color 0.3s; padding: 8px 0; border-bottom: 2px solid transparent; }
.sb-menu a:hover, .sb-menu a.active { color: #FF6B35; border-bottom-color: #FF6B35; }
.sb-daftar { padding: 10px 20px; border-radius: 25px; font-weight: 600; color: white; }
</style>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

window.onclick = function(event) {
    if (!event.target.matches('.sb-daftar') && !event.target.closest('.sb-daftar')) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown && dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    }
}
</script>

<?php else: ?>
<?php include '../../layouts/header.php'; ?>
<?php endif; ?>

<main style="background: #f8f9fa; min-height: 100vh; padding-top: <?php echo ($isLoggedIn && $userRole == 'learner') ? '20px' : '100px'; ?>;">
  <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 40px 120px;">

    <!-- TITLE & SEARCH -->
    <div style="background: white; padding: 40px 50px; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px;">
      <h2 style="font-size: 32px; font-weight: 700; color: #1e293b; margin: 0 0 30px 0;">Temukan Tutor Terbaik</h2>
      
      <form method="GET" action="search_result.php" style="display: flex; gap: 12px; margin-bottom: 35px;">
        <input type="text" id="searchInput" name="q" placeholder="Cari mata pelajaran, nama tutor, atau universitas..." 
               value="<?php echo htmlspecialchars($searchQuery); ?>"
               style="flex: 1; padding: 16px 24px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 15px; outline: none; transition: all 0.2s;"
               onfocus="this.style.borderColor='#FF6B35'"
               onblur="this.style.borderColor='#e2e8f0'">
        <button type="submit" style="background: #FF6B35; color: white; border: none; padding: 16px 40px; border-radius: 10px; font-weight: 600; cursor: pointer; font-size: 15px; white-space: nowrap; transition: all 0.2s;"
                onmouseover="this.style.background='#E55A2B'"
                onmouseout="this.style.background='#FF6B35'">
          <i class="bi bi-search"></i> Cari
        </button>
      </form>

      <!-- FILTERS -->
      <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
        <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-weight: 600; margin-right: 5px;">
          <i class="bi bi-funnel" style="font-size: 16px;"></i>
          <span>Filter:</span>
        </div>
        
        <button class="filter-btn" data-filter="SD">SD</button>
        <button class="filter-btn" data-filter="SMP">SMP</button>
        <button class="filter-btn active" data-filter="SMA">SMA</button>
        <button class="filter-btn" data-filter="Kuliah">Kuliah</button>
        
        <select id="mapelFilter" style="padding: 10px 18px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; font-size: 14px; color: #1e293b; font-weight: 500; outline: none;">
          <option value="">Matematika</option>
          <option value="Fisika">Fisika</option>
          <option value="Kimia">Kimia</option>
          <option value="Biologi">Biologi</option>
          <option value="Bahasa Inggris">Bahasa Inggris</option>
        </select>

        <button class="rating-filter active">4.5+ ⭐</button>
        <button class="rating-filter">4.0+ ⭐</button>
        
        <div style="display: flex; align-items: center; gap: 10px; margin-left: 5px;">
          <span style="color: #64748b; font-weight: 500; font-size: 14px;">Harga Maks:</span>
          <input type="range" id="priceRange" min="0" max="200" value="100" style="width: 140px; cursor: pointer;">
          <span id="priceLabel" style="background: #FF6B35; color: white; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; min-width: 50px; text-align: center;">100k</span>
        </div>

        <button onclick="resetFilters()" style="background: transparent; border: 2px solid #e2e8f0; color: #64748b; padding: 10px 18px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-left: auto; font-size: 14px; transition: all 0.2s;"
                onmouseover="this.style.borderColor='#FF6B35'; this.style.color='#FF6B35'"
                onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#64748b'">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
      </div>
    </div>

    <!-- RESULT INFO -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding: 0 4px;">
      <h3 style="color: #1e293b; font-size: 18px; font-weight: 600; margin: 0;">
        Menampilkan <span id="resultCount" style="color: #FF6B35;">51</span> Tutor
      </h3>
      <select id="sortBy" style="padding: 10px 18px; border: 2px solid #e2e8f0; border-radius: 8px; background: white; cursor: pointer; font-size: 14px; color: #1e293b; font-weight: 500; outline: none;">
        <option value="recommended">Rekomendasi</option>
        <option value="rating">Rating Tertinggi</option>
        <option value="price-low">Harga Terendah</option>
        <option value="price-high">Harga Tertinggi</option>
      </select>
    </div>

    <!-- SEARCH RESULT GRID -->
    <div id="resultContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; padding-bottom: 60px;">
      <!-- JS render -->
    </div>

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

// Generate random colors for avatar
function getAvatarColor(name) {
  const colors = ['#FF6B9D', '#4A90E2', '#50C878', '#FF6B35', '#9B59B6', '#E67E22', '#16A085', '#D35400'];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
}

// RENDER RESULT
function renderResults(list){
  const container = document.getElementById('resultContainer');
  container.innerHTML = "";
  
  document.getElementById('resultCount').textContent = list.length;

  if(list.length === 0){
    container.innerHTML = `
      <p style="grid-column:1/-1;text-align:center;color:#64748b;padding:60px 20px;">
        Tidak ada tutor ditemukan. Coba ubah filter pencarian Anda.
      </p>
    `;
    return;
  }

  list.forEach(t => {
    const card = document.createElement("div");
    card.className = "tutor-card-modern";
    
    const initials = t.nama.split(' ').map(n => n[0]).join('').substring(0, 2);
    const avatarColor = getAvatarColor(t.nama);
    const ratingStars = '⭐'.repeat(Math.floor(t.rating));

    card.innerHTML = `
      <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s; cursor: pointer; height: 100%;"
           onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'; this.style.transform='translateY(-4px)'"
           onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)'">
        
        <!-- Avatar & Verified Badge -->
        <div style="display: flex; align-items: start; gap: 16px; margin-bottom: 16px;">
          <div style="width: 56px; height: 56px; border-radius: 50%; background: ${avatarColor}; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; font-weight: 700; flex-shrink: 0;">
            ${initials}
          </div>
          <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
              <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                ${t.nama}
              </h3>
              <span style="color: #10b981; font-size: 14px;">✓</span>
            </div>
            <p style="color: #64748b; font-size: 13px; margin: 0;">${t.mapel || 'Umum'}</p>
          </div>
          <div style="background: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 4px;">
            ⭐ ${t.rating}
          </div>
        </div>

        <!-- Tags -->
        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px;">
          <span style="background: #DBEAFE; color: #1D4ED8; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
            Fisika SMA
          </span>
          <span style="background: #DBEAFE; color: #1D4ED8; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
            ${t.mapel}
          </span>
        </div>

        <!-- Stats -->
        <div style="display: flex; gap: 20px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
          <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px;">
            <i class="bi bi-clock"></i>
            <span>${Math.floor(Math.random() * 4) + 2} tahun</span>
          </div>
          <div style="display: flex; align-items: center; gap: 6px; color: #64748b; font-size: 13px;">
            <i class="bi bi-people"></i>
            <span>${Math.floor(Math.random() * 300) + 50} review</span>
          </div>
        </div>

        <!-- Price & Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <div>
            <div style="color: #FF6B35; font-size: 20px; font-weight: 700;">
              Rp ${Math.floor(t.harga / 1000)}k
            </div>
            <div style="color: #94a3b8; font-size: 12px;">/jam</div>
          </div>
        </div>

        <!-- Buttons -->
        <div style="display: grid; gap: 8px;">
          <button onclick="window.location.href='../learner/booking.php?tutor_id=${t.id}'" 
                  style="background: #FF6B35; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.2s;"
                  onmouseover="this.style.background='#E55A2B'"
                  onmouseout="this.style.background='#FF6B35'">
            <i class="bi bi-calendar-check"></i> Booking
          </button>
          <button onclick="window.location.href='detail_tutor.php?id=${t.id}'"
                  style="background: white; color: #FF6B35; border: 2px solid #FF6B35; padding: 10px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: all 0.2s;"
                  onmouseover="this.style.background='#FFF5F0'"
                  onmouseout="this.style.background='white'">
            <i class="bi bi-person"></i> Lihat Profil
          </button>
        </div>

      </div>
    `;

    container.appendChild(card);
  });
}

// INITIAL RENDER
renderResults(tutorsData);

// Filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
  });
});

// Rating filter
document.querySelectorAll('.rating-filter').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.rating-filter').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
  });
});

// Price range
const priceRangeInput = document.getElementById('priceRange');
const priceLabel = document.getElementById('priceLabel');
priceRangeInput.addEventListener('input', function() {
  priceLabel.textContent = this.value + 'k';
});

// Sort
document.getElementById('sortBy').addEventListener('change', function() {
  let sorted = [...tutorsData];
  switch(this.value) {
    case 'rating':
      sorted.sort((a, b) => b.rating - a.rating);
      break;
    case 'price-low':
      sorted.sort((a, b) => a.harga - b.harga);
      break;
    case 'price-high':
      sorted.sort((a, b) => b.harga - a.harga);
      break;
  }
  renderResults(sorted);
});

// Live search
document.getElementById('searchInput').addEventListener('input', function() {
  const q = this.value.toLowerCase().trim();
  const filtered = tutorsData.filter(t =>
    t.nama.toLowerCase().includes(q) || 
    (t.mapel && t.mapel.toLowerCase().includes(q))
  );
  renderResults(filtered);
});

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('priceRange').value = 100;
  document.getElementById('priceLabel').textContent = '100k';
  document.getElementById('sortBy').value = 'recommended';
  renderResults(tutorsData);
}
</script>

<style>
.filter-btn {
  padding: 10px 24px;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.filter-btn:hover {
  border-color: #FF6B35;
  color: #FF6B35;
  transform: translateY(-1px);
}

.filter-btn.active {
  background: #FF6B35;
  color: white;
  border-color: #FF6B35;
}

.rating-filter {
  padding: 10px 20px;
  border: 2px solid #e2e8f0;
  background: white;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.rating-filter:hover {
  border-color: #FEF3C7;
  background: #FEF3C7;
  color: #D97706;
  transform: translateY(-1px);
}

.rating-filter.active {
  background: #FEF3C7;
  color: #D97706;
  border-color: #FEF3C7;
}

/* Range slider styling */
#priceRange {
  -webkit-appearance: none;
  appearance: none;
  height: 6px;
  background: #e2e8f0;
  border-radius: 3px;
  outline: none;
}

#priceRange::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 18px;
  height: 18px;
  background: #FF6B35;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
}

#priceRange::-webkit-slider-thumb:hover {
  transform: scale(1.2);
}

#priceRange::-moz-range-thumb {
  width: 18px;
  height: 18px;
  background: #FF6B35;
  border-radius: 50%;
  cursor: pointer;
  border: none;
}
</style>

<?php if ($isLoggedIn && $userRole == 'learner' && $siswa_data): ?>
<script>
// Dropdown toggle function for learner navbar
function toggleDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.profile-trigger, .profile-trigger *')) {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown && dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
        }
    }
}
</script>
</body>
</html>
<?php else: ?>
<?php include '../../layouts/footer.php'; ?>
<?php endif; ?>
