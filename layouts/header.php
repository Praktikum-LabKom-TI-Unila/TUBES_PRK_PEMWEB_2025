<?php 
if (!isset($assetPath)) {
  $assetPath = "../../assets/";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>ScholarBridge</title>

  <!-- Bootstrap -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <!-- Icons -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $assetPath ?>css/style.css">
</head>

<body>

<!-- NAVBAR -->
<nav class="sb-navbar shadow-sm">
  <div class="container d-flex align-items-center justify-content-between">

    <!-- Logo -->
    <div class="d-flex align-items-center">
      <img src="<?php echo $assetPath ?>img/logo.png" class="sb-logo">
      <span class="sb-brand">ScholarBridge</span>
    </div>

    <!-- Menu -->
    <ul class="sb-menu">
      <li><a href="../public/landing_page.php" class="active">Beranda</a></li>
      <li><a href="../public/search_result.php">Cari Tutor</a></li>
      <li><a href="#">Kategori</a></li>
      <li><a href="#">Testimoni</a></li>
    </ul>

    <!-- Action -->
    <div class="d-flex gap-2">
      <a href="../auth/form_login.php" class="sb-login">Masuk</a>
      <a href="../auth/form_register.php" class="sb-daftar">Daftar</a>
    </div>

  </div>
</nav>
