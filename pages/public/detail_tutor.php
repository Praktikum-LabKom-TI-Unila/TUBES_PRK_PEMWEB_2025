<?php 
$assetPath = "../../assets/";
include '../../layouts/header.php';

// ambil data GET aman
$nama   = isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : "Nama Tutor";
$mapel  = isset($_GET['mapel']) ? htmlspecialchars($_GET['mapel']) : "Mata Pelajaran";
$harga  = isset($_GET['harga']) ? intval($_GET['harga']) : 300000;
$rating = isset($_GET['rating']) ? $_GET['rating'] : "4.8";

// ambil huruf profil
$initial = strtoupper(substr($nama,0,1));
?>

<div class="detail-wrapper">

  <!-- ================= PROFILE CARD ================= -->
  <div class="profile-main">

    <div class="detail-head">
      
      <!-- FOTO / ICON -->
      <div class="detail-avatar">
        <div style="
          width:100%;height:100%;display:flex;
          align-items:center;justify-content:center;
          font-size:60px;font-weight:800;color:#ff6b35;
        ">
          <?= $initial ?>
        </div>
      </div>

      <!-- INFO -->
      <div class="detail-info">
        <h2><?= $nama ?></h2>
        <div class="mapel"><?= $mapel ?></div>
        <div class="detail-rating">⭐ <?= $rating ?></div>

        <!-- PRICE BOX -->
        <div class="price-box mt-3">
          <div>Harga Sesi</div>
          <strong>Rp <?= number_format($harga,0,",",".") ?></strong>
        </div>

      </div>

    </div>

    <!-- ================= ABOUT ================= -->
    <div class="detail-section">
      <h3>Tentang Tutor</h3>
      <p>
        Halo! Saya <b><?= $nama ?></b>, tutor <?= strtolower($mapel); ?> yang telah berpengalaman 
        mengajar siswa SD, SMP, hingga SMA.  
        Saya mengandalkan metode pembelajaran interaktif dan latihan soal 
        agar siswa cepat memahami materi.
      </p>
    </div>

    <!-- ================= SKILLS ================= -->
    <div class="detail-section">
      <h3>Keahlian</h3>
      <div class="skill-badges">
        <span>Penguasaan Materi</span>
        <span>Pembelajaran Interaktif</span>
        <span>Latihan Intensif</span>
        <span>Persiapan Ujian</span>
        <span>Manajemen Waktu</span>
      </div>
    </div>

    <!-- ================= REVIEWS ================= -->
    <div class="detail-section">
      <h3>Ulasan Siswa</h3>

      <div class="review-card">
        <div class="review-user">Nadia</div>
        <div class="review-rating">⭐ 5.0</div>
        <p>“Penjelasannya sangat mudah dipahami, belajar jadi lebih menyenangkan.”</p>
      </div>

      <div class="review-card">
        <div class="review-user">Raka</div>
        <div class="review-rating">⭐ 4.8</div>
        <p>“Materi padat dan to the point, cocok buat persiapan ujian.”</p>
      </div>

      <div class="review-card">
        <div class="review-user">Sinta</div>
        <div class="review-rating">⭐ 4.9</div>
        <p>“Penyampaian materi rapi dan tutor sangat sabar.”</p>
      </div>

    </div>

    <!-- ================= CTA CONTACT ================= -->
    <div style="text-align:center; margin-top:30px;">
      <a href="#" class="cta-btn" style="text-decoration:none;">
        Hubungi Tutor
      </a>
    </div>

  </div>

</div>

<?php include '../../layouts/footer.php'; ?>
