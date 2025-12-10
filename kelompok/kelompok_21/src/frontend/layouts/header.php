<?php 
if (!isset($assetPath)) {
  $assetPath = "../../../assets/";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ScholarBridge</title>

  <!-- Bootstrap Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $assetPath ?>css/style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="sb-navbar">
  <div class="sb-nav-container">

    <!-- Logo/Brand -->
    <div class="sb-brand">
      <img src="../../../assets/img/logo.png" alt="ScholarBridge Logo" class="logo">
      <span>ScholarBridge</span>
    </div>

    <!-- Menu -->
    <ul class="sb-menu">
      <li><a href="../public/landing_page.php">Beranda</a></li>
      <li><a href="../public/search_result.php">Cari Tutor</a></li>
      <li><a href="#kategori">Kategori</a></li>
      <li><a href="#testimoni">Testimoni</a></li>
    </ul>

    <!-- Action Buttons -->
    <div style="display: flex; gap: 10px; align-items: center;">
      <a href="../auth/login.php" class="sb-login">Masuk</a>
      <a href="../auth/register.php" class="sb-daftar">Daftar</a>
    </div>

  </div>
</nav>
