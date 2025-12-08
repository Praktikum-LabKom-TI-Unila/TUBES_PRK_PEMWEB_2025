<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Bulan Indonesia array
$bulan_indo = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

// Determine filter type
$filter_type = $_GET['filter_type'] ?? 'date';
$where_clause = "1=1";
$date_label = "";

switch ($filter_type) {
    case 'date':
        $selected_date = $_GET['date'] ?? date('Y-m-d');
        $where_clause = "DATE(t.tgl_masuk) = '$selected_date'";
        $date_parts = explode('-', $selected_date);
        $date_label = $date_parts[2] . ' ' . $bulan_indo[$date_parts[1]] . ' ' . $date_parts[0];
        break;
        
    case 'month':
        $selected_month = $_GET['month'] ?? date('m');
        $selected_month_year = $_GET['month_year'] ?? date('Y');
        $where_clause = "MONTH(t.tgl_masuk) = '$selected_month' AND YEAR(t.tgl_masuk) = '$selected_month_year'";
        $date_label = $bulan_indo[$selected_month] . ' ' . $selected_month_year;
        break;
}

// Query untuk statistik
$stats_query = "SELECT 
                    COUNT(*) as total_transaksi,
                    SUM(CASE WHEN t.status_bayar = 'Paid' THEN 1 ELSE 0 END) as transaksi_lunas,
                    SUM(CASE WHEN t.status_bayar = 'Unpaid' THEN 1 ELSE 0 END) as transaksi_belum_lunas,
                    COALESCE(SUM(CASE WHEN t.status_bayar = 'Paid' THEN t.total_harga ELSE 0 END), 0) as total_pendapatan,
                    COALESCE(SUM(t.berat_qty), 0) as total_berat
                FROM transactions t
                WHERE $where_clause";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Handle NULL values
$stats['total_transaksi'] = $stats['total_transaksi'] ?? 0;
$stats['transaksi_lunas'] = $stats['transaksi_lunas'] ?? 0;
$stats['transaksi_belum_lunas'] = $stats['transaksi_belum_lunas'] ?? 0;
$stats['total_pendapatan'] = $stats['total_pendapatan'] ?? 0;
$stats['total_berat'] = $stats['total_berat'] ?? 0;

// Query untuk daftar transaksi
$transactions_query = "SELECT t.id, t.nama_pelanggan, t.no_hp, t.berat_qty, t.total_harga,
                              t.status_laundry, t.status_bayar, t.tgl_masuk, t.tgl_estimasi_selesai,
                              p.nama_paket, p.satuan,
                              u.full_name as kasir_nama
                       FROM transactions t
                       JOIN packages p ON t.package_id = p.id
                       LEFT JOIN users u ON t.kasir_input_id = u.id
                       WHERE $where_clause
                       ORDER BY t.tgl_masuk DESC";
$transactions_result = mysqli_query($conn, $transactions_query);

$page_title = "Laporan Transaksi";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - E-Laundry</title>
    <link rel="stylesheet" href="../../assets/css/admin.css?v=<?php echo time(); ?>">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            color: #333;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .filter-dropdown-wrapper {
            position: relative;
        }

        .btn-filter {
            background: #008080;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-filter:hover {
            background: #006666;
        }

        .filter-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            min-width: 320px;
            z-index: 1000;
            display: none;
        }

        .filter-dropdown-menu.show {
            display: block;
        }

        .filter-dropdown-header {
            padding: 15px 20px;
            border-bottom: 2px solid #E0E0E0;
            font-weight: 600;
            color: #008080;
            font-size: 15px;
        }

        .filter-option-group {
            padding: 15px 20px;
        }

        .filter-option-title {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-date-input,
        .filter-month-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            color: #333;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-date-input:hover,
        .filter-month-select:hover {
            border-color: #008080;
            background: #F8FFFE;
        }

        .filter-date-input:focus,
        .filter-month-select:focus {
            outline: none;
            border-color: #008080;
            box-shadow: 0 0 0 3px rgba(0, 128, 128, 0.1);
        }

        .filter-month-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .filter-divider {
            height: 1px;
            background: #E0E0E0;
            margin: 0;
        }

        .filter-date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
        }

        .filter-date-input::-webkit-calendar-picker-indicator:hover {
            background: #E6F7F7;
        }

        .btn-export {
            background: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-export:hover {
            background: #218838;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
        }

        .stat-card h3 {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            font-weight: 500;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #008080;
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-card .subtitle {
            font-size: 12px;
            color: #999;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 18px;
            color: #333;
        }

        .date-range-label {
            background: #E3F2FD;
            color: #1976D2;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #F8F9FA;
            padding: 12px 15px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        tbody td {
            padding: 15px;
            border-top: 1px solid #F0F0F0;
            font-size: 13px;
            color: #333;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #F8FFFE;
        }

        tbody td:first-child {
            font-weight: 600;
        }

        .transaction-id {
            font-weight: 600;
            color: #008080;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-pending { background: #FFF3CD; color: #856404; }
        .badge-washing { background: #CCE5FF; color: #004085; }
        .badge-ironing { background: #D1ECF1; color: #0C5460; }
        .badge-done { background: #D4EDDA; color: #155724; }
        .badge-taken { background: #E2E3E5; color: #383D41; }
        .badge-paid { background: #D4EDDA; color: #155724; }
        .badge-unpaid { background: #F8D7DA; color: #721C24; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            opacity: 0.3;
        }
    </style>
    <script>
        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterDropdown');
            const wrapper = document.querySelector('.filter-dropdown-wrapper');
            
            if (wrapper && !wrapper.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
</head>
<body>
    <div class="admin-container">
        <?php include '../../includes/sidebar_admin.php'; ?>
        
        <main class="main-content">
            <?php include '../../includes/header_admin.php'; ?>
            
            <div class="content-wrapper">
                <div class="page-header">
                    <h1>Laporan Transaksi</h1>
                    <div class="header-actions">
                        <!-- Filter Button with Dropdown -->
                        <div class="filter-dropdown-wrapper">
                            <button type="button" class="btn-filter" onclick="toggleFilterDropdown()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Filter Periode
                            </button>
                            
                            <div class="filter-dropdown-menu" id="filterDropdown">
                                <div class="filter-dropdown-header">Pilih Periode Laporan</div>
                                
                                <!-- Filter by Date -->
                                <div class="filter-option-group">
                                    <div class="filter-option-title">Per Tanggal</div>
                                    <form method="GET" class="filter-form">
                                        <input type="hidden" name="filter_type" value="date">
                                        <input type="date" name="date" class="filter-date-input" 
                                               value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>" 
                                               onchange="this.form.submit()">
                                    </form>
                                </div>
                                
                                <div class="filter-divider"></div>
                                
                                <!-- Filter by Month -->
                                <div class="filter-option-group">
                                    <div class="filter-option-title">Per Bulan</div>
                                    <form method="GET" class="filter-form">
                                        <input type="hidden" name="filter_type" value="month">
                                        <div class="filter-month-inputs">
                                            <select name="month" class="filter-month-select" onchange="this.form.submit()">
                                                <option value="">- Pilih Bulan -</option>
                                                <?php
                                                $bulan_list = [
                                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                                ];
                                                $selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
                                                foreach($bulan_list as $num => $nama) {
                                                    $sel = ($selected_month == $num) ? 'selected' : '';
                                                    echo "<option value='$num' $sel>$nama</option>";
                                                }
                                                ?>
                                            </select>
                                            <select name="month_year" class="filter-month-select" onchange="this.form.submit()">
                                                <option value="">- Tahun -</option>
                                                <?php
                                                $selected_year = isset($_GET['month_year']) ? $_GET['month_year'] : date('Y');
                                                for($y = date('Y'); $y >= 2020; $y--) {
                                                    $sel = ($selected_year == $y) ? 'selected' : '';
                                                    echo "<option value='$y' $sel>$y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Export Button -->
                        <a href="#" onclick="window.print(); return false;" class="btn-export">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8"></rect>
                            </svg>
                            Export/Print
                        </a>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total Transaksi</h3>
                        <div class="number"><?php echo number_format((int)$stats['total_transaksi']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-card">
                        <h3>Transaksi Lunas</h3>
                        <div class="number"><?php echo number_format((int)$stats['transaksi_lunas']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-card">
                        <h3>Belum Lunas</h3>
                        <div class="number"><?php echo number_format((int)$stats['transaksi_belum_lunas']); ?></div>
                        <div class="subtitle">transaksi</div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Pendapatan</h3>
                        <div class="number" style="font-size: 24px;">Rp <?php echo number_format((float)$stats['total_pendapatan'], 0, ',', '.'); ?></div>
                        <div class="subtitle">dari transaksi lunas</div>
                    </div>
                    <div class="stat-card">
                        <h3>Total Cucian</h3>
                        <div class="number"><?php echo number_format((float)$stats['total_berat'], 1, ',', '.'); ?></div>
                        <div class="subtitle">kg/pcs</div>
                    </div>
                </div>

                <!-- Transactions Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h2>Daftar Transaksi</h2>
                        <div class="date-range-label">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <?php echo $date_label; ?>
                        </div>
                    </div>
                    
                    <?php if (mysqli_num_rows($transactions_result) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Kode Resi</th>
                                    <th style="width: 140px;">Tanggal</th>
                                    <th style="width: 180px;">Pelanggan</th>
                                    <th style="width: 150px;">Paket</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th style="width: 120px;">Total</th>
                                    <th style="width: 130px;">Status Laundry</th>
                                    <th style="width: 120px;">Status Bayar</th>
                                    <th style="width: 120px;">Kasir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($trx = mysqli_fetch_assoc($transactions_result)): ?>
                                <tr>
                                    <td><span class="transaction-id"><?php echo htmlspecialchars($trx['id']); ?></span></td>
                                    <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($trx['tgl_masuk'])); ?></td>
                                    <td>
                                        <div style="line-height: 1.4;">
                                            <strong style="display: block; margin-bottom: 3px;"><?php echo htmlspecialchars($trx['nama_pelanggan']); ?></strong>
                                            <small style="color: #666;"><?php echo htmlspecialchars($trx['no_hp']); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($trx['nama_paket']); ?></td>
                                    <td><?php echo $trx['berat_qty']; ?> <?php echo strtoupper($trx['satuan']); ?></td>
                                    <td><strong style="color: #008080;">Rp <?php echo number_format($trx['total_harga'], 0, ',', '.'); ?></strong></td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($trx['status_laundry']); ?>">
                                            <?php echo $trx['status_laundry']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($trx['status_bayar']); ?>">
                                            <?php echo $trx['status_bayar'] === 'Paid' ? 'Lunas' : 'Belum Lunas'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($trx['kasir_nama'] ?? '-'); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        <p>Tidak ada transaksi pada periode yang dipilih</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>


</body>
</html>
