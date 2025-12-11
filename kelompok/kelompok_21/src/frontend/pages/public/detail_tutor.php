<?php 
$assetPath = "../../assets/";
include '../../layouts/header.php';

$nama   = isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : "Nama Tutor";
$mapel  = isset($_GET['mapel']) ? htmlspecialchars($_GET['mapel']) : "Mata Pelajaran";
$harga  = isset($_GET['harga']) ? intval($_GET['harga']) : 300000;
$rating = isset($_GET['rating']) ? $_GET['rating'] : "4.8";

$initial = strtoupper(substr($nama,0,1));
?>

<div class="detail-wrapper">

  <div class="profile-main">

    <div class="detail-head">

      <div class="detail-avatar">
        <div style="
          width:100%;height:100%;display:flex;
          align-items:center;justify-content:center;
          font-size:60px;font-weight:800;color:#ff6b35;
        ">
          <?= $initial ?>
        </div>
      </div>

      <div class="detail-info">
        <h2><?= $nama ?></h2>
        <div class="mapel"><?= $mapel ?></div>
        <div class="detail-rating">⭐ <?= $rating ?></div>

        <div class="price-box mt-3">
          <div>Harga Sesi</div>
          <strong>Rp <?= number_format($harga,0,",",".") ?></strong>
        </div>

      </div>

    </div>

    <div class="detail-section">
      <h3>Tentang Tutor</h3>
      <p>
        Halo! Saya <b><?= $nama ?></b>, tutor <?= strtolower($mapel); ?> yang telah berpengalaman 
        mengajar siswa SD, SMP, hingga SMA.  
        Saya mengandalkan metode pembelajaran interaktif dan latihan soal 
        agar siswa cepat memahami materi.
      </p>
    </div>

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

    <div style="text-align:center; margin-top:30px;">
      <?php
      if (session_status() === PHP_SESSION_NONE) {
          session_start();
      }
      $tutor_id_param = isset($_GET['tutor_id']) ? intval($_GET['tutor_id']) : 0;
      $subject_id_param = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
      
      if (isset($_SESSION['role']) && $_SESSION['role'] == 'learner') {
          if ($tutor_id_param > 0) {
              $booking_url = "../../frontend/pages/learner/booking.php?tutor_id=" . $tutor_id_param;
              if ($subject_id_param > 0) {
                  $booking_url .= "&subject_id=" . $subject_id_param;
              }
              echo '<a href="' . $booking_url . '" class="cta-btn" style="text-decoration:none; display:inline-block; padding:12px 30px; background:linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); color:white; border-radius:8px; font-weight:600;">Booking Sekarang</a>';
          } else {
              $db_path = dirname(__DIR__, 2) . '/config/database.php';
              if (file_exists($db_path)) {
                  require_once $db_path;
                  $nama_escaped = mysqli_real_escape_string($conn, $nama);
                  $tutor_query = "SELECT id FROM users WHERE name LIKE '%$nama_escaped%' AND role = 'tutor' AND status = 'active' LIMIT 1";
                  $tutor_result = mysqli_query($conn, $tutor_query);
                  if ($tutor_result && mysqli_num_rows($tutor_result) > 0) {
                      $tutor_row = mysqli_fetch_assoc($tutor_result);
                      $booking_url = "../../frontend/pages/learner/booking.php?tutor_id=" . $tutor_row['id'];
                      echo '<a href="' . $booking_url . '" class="cta-btn" style="text-decoration:none; display:inline-block; padding:12px 30px; background:linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); color:white; border-radius:8px; font-weight:600;">Booking Sekarang</a>';
                  } else {
                      echo '<a href="../../frontend/pages/auth/login.php" class="cta-btn" style="text-decoration:none; display:inline-block; padding:12px 30px; background:linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); color:white; border-radius:8px; font-weight:600;">Login untuk Booking</a>';
                  }
              } else {
                  echo '<a href="../../frontend/pages/auth/login.php" class="cta-btn" style="text-decoration:none; display:inline-block; padding:12px 30px; background:linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); color:white; border-radius:8px; font-weight:600;">Login untuk Booking</a>';
              }
          }
      } else {
          echo '<a href="../../frontend/pages/auth/login.php" class="cta-btn" style="text-decoration:none; display:inline-block; padding:12px 30px; background:linear-gradient(135deg, #FF6B35 0%, #F7931E 100%); color:white; border-radius:8px; font-weight:600;">Login untuk Booking</a>';
      }
      ?>
    </div>

  </div>

</div>

<?php include '../../layouts/footer.php'; ?>
