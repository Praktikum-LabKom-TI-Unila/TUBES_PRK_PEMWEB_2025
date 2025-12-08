<?php

require_once BASE_PATH . '/src/app/views/layouts/header.php';
?>

<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="glass-effect rounded-2xl p-6 mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
          <i data-lucide="file-text" class="w-8 h-8"></i>
          Laporan Penjualan
        </h1>
        <p class="text-blue-100">Kelola dan analisis data penjualan Anda</p>
      </div>
      <a href="<?= BASE_URL ?>/report/dashboard" class="glass-effect px-6 py-3 rounded-xl hover:bg-white/20 transition flex items-center gap-2">
        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
        Dashboard
      </a>
    </div>
  </div>

  <!-- Filter Section -->
  <div class="glass-effect rounded-2xl p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
      <i data-lucide="filter" class="w-5 h-5"></i>
      Filter Laporan
    </h2>
    
    <form method="GET" action="<?= BASE_URL ?>/report/index" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-medium mb-2">Tanggal Mulai</label>
        <input type="date" name="start_date" value="<?= htmlspecialchars($filter['start_date']) ?>" 
               class="w-full px-4 py-2 rounded-xl bg-white/20 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Tanggal Akhir</label>
        <input type="date" name="end_date" value="<?= htmlspecialchars($filter['end_date']) ?>" 
               class="w-full px-4 py-2 rounded-xl bg-white/20 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
      </div>
      
      <div>
        <label class="block text-sm font-medium mb-2">Produk</label>
        <select name="product_id" class="w-full px-4 py-2 rounded-xl bg-white/20 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
          <option value="">Semua Produk</option>
          <?php foreach ($products as $product): ?>
            <option value="<?= $product['id'] ?>" <?= $filter['product_id'] == $product['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($product['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 bg-white/30 hover:bg-white/40 px-6 py-2 rounded-xl transition flex items-center justify-center gap-2">
          <i data-lucide="search" class="w-4 h-4"></i>
          Filter
        </button>
        <a href="<?= BASE_URL ?>/report/index" class="glass-effect px-4 py-2 rounded-xl hover:bg-white/20 transition">
          <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
        </a>
      </div>
    </form>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="glass-effect rounded-2xl p-6">
      <div class="flex items-center gap-4">
        <div class="bg-green-400/30 p-4 rounded-xl">
          <i data-lucide="shopping-cart" class="w-8 h-8"></i>
        </div>
        <div>
          <p class="text-sm text-blue-100">Total Transaksi</p>
          <p class="text-3xl font-bold"><?= number_format($summary['total_transactions']) ?></p>
        </div>
      </div>
    </div>

    <div class="glass-effect rounded-2xl p-6">
      <div class="flex items-center gap-4">
        <div class="bg-yellow-400/30 p-4 rounded-xl">
          <i data-lucide="package" class="w-8 h-8"></i>
        </div>
        <div>
          <p class="text-sm text-blue-100">Total Item Terjual</p>
          <p class="text-3xl font-bold"><?= number_format($summary['total_items']) ?></p>
        </div>
      </div>
    </div>

    <div class="glass-effect rounded-2xl p-6">
      <div class="flex items-center gap-4">
        <div class="bg-blue-400/30 p-4 rounded-xl">
          <i data-lucide="dollar-sign" class="w-8 h-8"></i>
        </div>
        <div>
          <p class="text-sm text-blue-100">Total Pendapatan</p>
          <p class="text-3xl font-bold">Rp <?= number_format($summary['total_revenue'], 0, ',', '.') ?></p>
        </div>
      </div>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="glass-effect rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-xl font-semibold flex items-center gap-2">
        <i data-lucide="list" class="w-5 h-5"></i>
        Daftar Transaksi
      </h2>
      <button onclick="printReport()" class="glass-effect px-6 py-2 rounded-xl hover:bg-white/20 transition flex items-center gap-2">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Print Laporan
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-white/20">
            <th class="text-left py-3 px-4">ID</th>
            <th class="text-left py-3 px-4">Tanggal</th>
            <th class="text-left py-3 px-4">Kasir</th>
            <th class="text-left py-3 px-4">Produk</th>
            <th class="text-right py-3 px-4">Total</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($transactions)): ?>
            <tr>
              <td colspan="5" class="text-center py-8 text-blue-100">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 opacity-50"></i>
                <p>Tidak ada transaksi ditemukan</p>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($transactions as $transaction): ?>
              <tr class="border-b border-white/10 hover:bg-white/10 transition">
                <td class="py-3 px-4">#<?= $transaction['id'] ?></td>
                <td class="py-3 px-4"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($transaction['cashier_name']) ?></td>
                <td class="py-3 px-4 text-sm"><?= htmlspecialchars($transaction['products']) ?></td>
                <td class="py-3 px-4 text-right font-semibold">Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function printReport() {
    const params = new URLSearchParams(window.location.search);
    window.open('<?= BASE_URL ?>/report/print?' + params.toString(), '_blank');
  }
  
  lucide.createIcons();
</script>

<?php require_once BASE_PATH . '/src/app/views/layouts/footer.php'; ?>