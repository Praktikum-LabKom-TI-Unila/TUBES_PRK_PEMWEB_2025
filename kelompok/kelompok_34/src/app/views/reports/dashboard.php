<div class="max-w-7xl mx-auto">
  <!-- Header -->
  <div class="glass-effect rounded-2xl p-6 mb-6">
    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-500/40 text-green-200 p-4 mb-6 rounded-xl border border-green-400/60 text-sm flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <span><?= $_SESSION['success'] ?></span>
      </div>
    <?php unset($_SESSION['success']);
    endif; ?>

    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
          <i data-lucide="layout-dashboard" class="w-8 h-8"></i>
          Dashboard Admin
        </h1>
        <p class="text-blue-100">Statistik dan analisis penjualan real-time</p>
      </div>
      <a href="<?= BASE_URL ?>/report/index" class="glass-effect px-6 py-3 rounded-xl hover:bg-white/20 transition flex items-center gap-2">
        <i data-lucide="file-text" class="w-5 h-5"></i>
        Laporan Detail
      </a>
    </div>
  </div>

  <!-- Statistics Cards - Horizontal -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Today Stats -->
    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-transform">
      <div class="flex items-center gap-4 mb-4">
        <div class="bg-green-400/30 p-3 rounded-xl">
          <i data-lucide="calendar-days" class="w-8 h-8"></i>
        </div>
        <h3 class="text-xl font-semibold">Hari Ini</h3>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Transaksi</p>
          <p class="text-3xl font-bold"><?= number_format($stats['today']['transactions']) ?></p>
        </div>
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Pendapatan</p>
          <p class="text-2xl font-bold">Rp <?= number_format($stats['today']['revenue'] / 1000, 0) ?>k</p>
        </div>
      </div>
    </div>

    <!-- Month Stats -->
    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-transform">
      <div class="flex items-center gap-4 mb-4">
        <div class="bg-yellow-400/30 p-3 rounded-xl">
          <i data-lucide="calendar" class="w-8 h-8"></i>
        </div>
        <h3 class="text-xl font-semibold">Bulan Ini</h3>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Transaksi</p>
          <p class="text-3xl font-bold"><?= number_format($stats['month']['transactions']) ?></p>
        </div>
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Pendapatan</p>
          <p class="text-2xl font-bold">Rp <?= number_format($stats['month']['revenue'] / 1000, 0) ?>k</p>
        </div>
      </div>
    </div>

    <!-- Year Stats -->
    <div class="glass-effect rounded-2xl p-6 hover:scale-105 transition-transform">
      <div class="flex items-center gap-4 mb-4">
        <div class="bg-blue-400/30 p-3 rounded-xl">
          <i data-lucide="calendar-range" class="w-8 h-8"></i>
        </div>
        <h3 class="text-xl font-semibold">Tahun Ini</h3>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Transaksi</p>
          <p class="text-3xl font-bold"><?= number_format($stats['year']['transactions']) ?></p>
        </div>
        <div class="bg-white/10 p-4 rounded-xl">
          <p class="text-sm text-blue-100 mb-1">Pendapatan</p>
          <p class="text-2xl font-bold">Rp <?= number_format($stats['year']['revenue'] / 1000, 0) ?>k</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section - Two Columns -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Daily Sales Chart (7 Days) -->
    <div class="glass-effect rounded-2xl p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
        <i data-lucide="calendar-days" class="w-5 h-5"></i>
        Grafik Harian (7 Hari Terakhir)
      </h2>
      <div class="bg-white/10 rounded-xl p-4">
        <canvas id="dailyChart" class="w-full" height="180"></canvas>
      </div>
    </div>

    <!-- Monthly Sales Chart (6 Months) -->
    <div class="glass-effect rounded-2xl p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
        <i data-lucide="trending-up" class="w-5 h-5"></i>
        Grafik Bulanan (6 Bulan Terakhir)
      </h2>
      <div class="bg-white/10 rounded-xl p-4">
        <canvas id="monthlyChart" class="w-full" height="180"></canvas>
      </div>
    </div>
  </div>

  <!-- Bottom Section - Two Columns -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Top Products -->
    <div class="glass-effect rounded-2xl p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
        <i data-lucide="trophy" class="w-5 h-5"></i>
        Top 5 Produk Terlaris
      </h2>
      <div class="space-y-3">
        <?php foreach ($stats['top_products'] as $index => $product): ?>
          <div class="flex items-center gap-4 p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
            <div class="text-2xl font-bold text-yellow-300 w-8">#<?= $index + 1 ?></div>
            <div class="flex-1">
              <p class="font-semibold"><?= htmlspecialchars($product['name']) ?></p>
              <p class="text-sm text-blue-100"><?= number_format($product['total_sold']) ?> terjual • Rp <?= number_format($product['total_revenue'], 0, ',', '.') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="glass-effect rounded-2xl p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
        <i data-lucide="clock" class="w-5 h-5"></i>
        Transaksi Terbaru
      </h2>
      <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
        <?php foreach ($stats['recent_transactions'] as $transaction): ?>
          <div class="flex items-center justify-between p-3 bg-white/10 rounded-xl hover:bg-white/20 transition">
            <div class="flex-1">
              <p class="font-semibold">#<?= $transaction['id'] ?> - <?= htmlspecialchars($transaction['cashier_name']) ?></p>
              <p class="text-sm text-blue-100"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></p>
            </div>
            <div class="text-right">
              <p class="font-bold text-lg">Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // ========== DAILY CHART (7 Days) ==========
  const dailyChartData = <?= json_encode($stats['daily_chart']) ?>;
  const dailyLabels = dailyChartData.map(item => {
    const date = new Date(item.date);
    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    return days[date.getDay()] + ', ' + date.getDate() + '/' + (date.getMonth() + 1);
  });
  const dailyRevenues = dailyChartData.map(item => parseFloat(item.revenue));

  const dailyCtx = document.getElementById('dailyChart').getContext('2d');
  new Chart(dailyCtx, {
    type: 'bar',
    data: {
      labels: dailyLabels,
      datasets: [{
        label: 'Pendapatan (Rp)',
        data: dailyRevenues,
        backgroundColor: 'rgba(52, 211, 153, 0.7)',
        borderColor: 'rgba(52, 211, 153, 1)',
        borderWidth: 2,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            color: 'white',
            font: {
              size: 13,
              weight: 'bold'
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: 'rgba(255, 255, 255, 0.3)',
          borderWidth: 1,
          padding: 12,
          displayColors: false,
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: 'white',
            font: {
              size: 11
            },
            callback: function(value) {
              return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
            }
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.1)',
            lineWidth: 1
          }
        },
        x: {
          ticks: {
            color: 'white',
            font: {
              size: 10
            }
          },
          grid: {
            display: false
          }
        }
      }
    }
  });

  // ========== MONTHLY CHART (6 Months) ==========
  const monthlyChartData = <?= json_encode($stats['monthly_chart']) ?>;
  const monthlyLabels = monthlyChartData.map(item => {
    const [year, month] = item.month.split('-');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
    return monthNames[parseInt(month) - 1] + ' ' + year;
  });
  const monthlyRevenues = monthlyChartData.map(item => parseFloat(item.revenue));

  const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
  new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: monthlyLabels,
      datasets: [{
        label: 'Pendapatan (Rp)',
        data: monthlyRevenues,
        borderColor: 'rgba(96, 165, 250, 1)',
        backgroundColor: 'rgba(96, 165, 250, 0.2)',
        borderWidth: 3,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: 'rgba(96, 165, 250, 1)',
        pointBorderColor: 'rgba(255, 255, 255, 1)',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
          labels: {
            color: 'white',
            font: {
              size: 13,
              weight: 'bold'
            }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: 'rgba(255, 255, 255, 0.3)',
          borderWidth: 1,
          padding: 12,
          displayColors: false,
          callbacks: {
            label: function(context) {
              return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: 'white',
            font: {
              size: 11
            },
            callback: function(value) {
              return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
            }
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.1)',
            lineWidth: 1
          }
        },
        x: {
          ticks: {
            color: 'white',
            font: {
              size: 10
            }
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.1)',
            lineWidth: 1
          }
        }
      }
    }
  });

  lucide.createIcons();
</script>