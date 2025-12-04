<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
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
  <?php include_once __DIR__ . '/../assets/style.php'; ?>
</head>

<body class="text-white bg-gradient-to-br from-blue-500 to-blue-600 relative min-h-screen overflow-x-hidden">
  <nav class="w-full fixed top-0 left-0 right-0 z-50 px-4 py-4">
    <div class="container mx-auto">
      <div class="glass-effect rounded-2xl py-4 px-8 flex items-center justify-between">

        <a href="<?= BASE_URL ?>/home" class="font-bold text-xl flex items-center gap-2 hover:scale-105 transition-transform">
          <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-2 rounded-xl">
            <i data-lucide="shopping-bag" class="w-6 h-6"></i>
          </div>
          <span class="bg-gradient-to-r from-blue-100 to-blue-200 bg-clip-text text-transparent"><?= APP_NAME ?></span>
        </a>

        <ul class="flex gap-8 text-sm font-medium">
          <?php if (!empty($_SESSION['user'])): ?>
            <li>
              <a href="<?= BASE_URL ?>" class="nav-link hover:text-blue-200 transition flex items-center gap-2">
                <i data-lucide="grid" class="w-4 h-4"></i>
                Daftar Menu
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