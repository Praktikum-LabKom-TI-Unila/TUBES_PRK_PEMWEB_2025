<?php 
if (!isset($assetPath)) {
  $assetPath = "../../assets/";
}
?>

<div class="sb-sidebar">
  
  <!-- Brand -->
  <div class="sb-side-header">
    <img src="<?php echo $assetPath ?>img/logo.png" class="sb-side-logo">
    <span class="sb-side-title">ScholarBridge</span>
  </div>

  <!-- Menu -->
  <ul class="sb-side-menu">

    <li>
      <a href="../tutor/dashboard_tutor.php">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>

    <li>
      <a href="../tutor/form_iklan.php">
        <i class="bi bi-megaphone"></i> Buat Iklan
      </a>
    </li>

    <li>
      <a href="../tutor/update_profile.php">
        <i class="bi bi-person"></i> Profil Saya
      </a>
    </li>

    <li>
      <a href="#">
        <i class="bi bi-chat-text"></i> Pesan
      </a>
    </li>

    <li>
      <a href="../auth/logout.php" class="sb-side-logout">
        <i class="bi bi-box-arrow-right"></i> Keluar
      </a>
    </li>

  </ul>

</div>
