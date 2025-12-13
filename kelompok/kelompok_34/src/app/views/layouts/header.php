<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
// Load Midtrans config for POS pages
if (strpos($_SERVER['REQUEST_URI'], '/pos') !== false) {
  require_once BASE_PATH . '/src/app/config/midtrans.php';
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <title><?= isset($title) ? htmlspecialchars($title) : APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/style.css">
</head>

<body class="text-white bg-gradient-to-br from-blue-500 to-blue-600 relative min-h-screen overflow-x-hidden">
  <nav class="w-full fixed top-0 left-0 right-0 z-50 px-4 py-4 bg-transparent">
    <div class="container mx-auto">
      <div class="glass-effect rounded-2xl py-4 px-8 flex items-center justify-between">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/home" class="font-bold text-xl flex items-center gap-2 hover:scale-105 transition-transform">
          <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-2 rounded-xl">
            <i data-lucide="shopping-bag" class="w-6 h-6"></i>
          </div>
          <span class="bg-gradient-to-r from-blue-100 to-blue-200 bg-clip-text text-transparent"><?= APP_NAME ?></span>
        </a>

        <!-- Hamburger (Mobile) -->
        <button id="nav-toggle" class="lg:hidden focus:outline-none">
          <i data-lucide="menu" class="w-7 h-7 text-white"></i>
        </button>

        <!-- Menu -->
        <ul id="nav-menu" class="hidden lg:flex gap-8 text-sm font-medium lg:static absolute top-full left-0 w-full lg:w-auto 
          lg:bg-transparent bg-black/40  
          lg:p-0 p-6 rounded-xl lg:rounded-none flex-col lg:flex-row space-y-4 lg:space-y-0">

          <?php if (!empty($_SESSION['user'])): ?>

            <?php if ($_SESSION['user']['role'] === 'seller'): ?>
              <li>
                <a href="<?= BASE_URL ?>/pos" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                  <i data-lucide="grid" class="w-4 h-4"></i>
                  Daftar Menu
                </a>
              </li>
            <?php endif; ?>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
              <li>
                <a href="<?= BASE_URL ?>/report/dashboard" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                  <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                  Dashboard Admin
                </a>
              </li>
              <li>
                <a href="<?= BASE_URL ?>/report" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                  <i data-lucide="file-text" class="w-4 h-4"></i>
                  Laporan
                </a>
              </li>
              <li>
                <a href="<?= BASE_URL ?>/product" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                  <i data-lucide="box" class="w-4 h-4"></i>
                  Produk
                </a>
              </li>
            <?php endif; ?>

            <li>
              <a href="<?= BASE_URL ?>/help" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                <i data-lucide="help-circle" class="w-4 h-4"></i>
                Bantuan
              </a>
            </li>

            <li>
              <a href="<?= BASE_URL ?>/auth/logout" class="nav-link hover:text-red-300 transition flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Logout
              </a>
            </li>

          <?php else: ?>
            <li>
              <a href="<?= BASE_URL ?>/auth/login" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i>
                Login
              </a>
            </li>
          <?php endif; ?>
        </ul>

      </div>
    </div>
  </nav>

  <div class="container mx-auto px-4 pt-28 pb-10 relative z-10">