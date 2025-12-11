<?php
session_start();
require_once '../../koneksi/database.php';
if (!isset($_SESSION['user'])) {
    header("Location: ../login/login.php");
    exit;
}
$role = strtoupper($_SESSION['user']['role']);
$id_user = $_SESSION['user']['id'];
$full_name = $_SESSION['user']['full_name'];
$username = $_SESSION['user']['username'] ?? 'User';
$pesan_aksi = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($role !== 'CUSTOMER')) {
    function catatLog($conn, $uid, $user, $role, $action, $target, $desc) {
        $desc = mysqli_real_escape_string($conn, $desc);
        $sql = "INSERT INTO system_logs (user_id, username, role, action_type, target_id, description) VALUES ('$uid', '$user', '$role', '$action', '$target', '$desc')";
        @mysqli_query($conn, $sql);
    }
    if (isset($_POST['act_next_stage'])) {
        $oid = (int)$_POST['order_id'];
        $current = $_POST['current_status'];
        $next = ''; $desc = '';
        if ($current == 'pending') { $next = 'processing'; $desc = 'Memulai proses produksi'; }
        elseif ($current == 'processing') { $next = 'ready'; $desc = 'Selesai produksi (Barang Siap Diambil)'; }
        elseif ($current == 'ready') { $next = 'completed'; $desc = 'Barang diserahkan (Transaksi Selesai)'; }
        if ($next) {
            $sql = "UPDATE orders SET status = '$next', updated_at = NOW() WHERE id = $oid";
            if ($next == 'completed') {
                $sql = "UPDATE orders SET status = '$next', payment_status = 'paid', updated_at = NOW() WHERE id = $oid";
            }
            if (mysqli_query($conn, $sql)) {
                $actorLabel = ucfirst(strtolower($role));
                $transitionLog = sprintf('%s mengubah status dari %s ke %s. %s', $actorLabel, strtoupper($current), strtoupper($next), $desc);
                catatLog($conn, $id_user, $username, $role, 'UPDATE_STATUS', $oid, $transitionLog);
                $pesan_aksi = "Status berhasil diubah menjadi: " . strtoupper($next);
            }
        }
    }
    if (isset($_POST['act_pay'])) {
        $oid = (int)$_POST['order_id'];
        $sql = "UPDATE orders SET payment_status = 'paid', updated_at = NOW() WHERE id = $oid";
        if (mysqli_query($conn, $sql)) {
            $actorLabel = ucfirst(strtolower($role));
            $logDesc = sprintf('%s mengonfirmasi pembayaran menjadi lunas.', $actorLabel);
            catatLog($conn, $id_user, $username, $role, 'UPDATE_PAYMENT', $oid, $logDesc);
            $pesan_aksi = "Pembayaran dikonfirmasi LUNAS.";
        }
    }
    if (isset($_POST['act_cancel'])) {
        $oid = (int)$_POST['order_id'];
        $reason = bersihkan_input($_POST['reason']);
        $sql = "UPDATE orders SET status = 'cancelled', notes = CONCAT(IFNULL(notes, ''), ' [Cancel: $reason]') WHERE id = $oid";
        if (mysqli_query($conn, $sql)) {
            $actorLabel = ucfirst(strtolower($role));
            $logDesc = sprintf('%s membatalkan pesanan. Alasan: %s', $actorLabel, $reason);
            catatLog($conn, $id_user, $username, $role, 'CANCEL_ORDER', $oid, $logDesc);
            $pesan_aksi = "Pesanan dibatalkan.";
        }
    }
}
$search = isset($_GET['search']) ? bersihkan_input($_GET['search']) : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$page_act = isset($_GET['page_act']) ? (int)$_GET['page_act'] : 1;
$page_hist = isset($_GET['page_hist']) ? (int)$_GET['page_hist'] : 1;
$limit_act_val = isset($_GET['limit_act']) ? $_GET['limit_act'] : 5;
$limit_hist_val = isset($_GET['limit_hist']) ? $_GET['limit_hist'] : 10;
function buildUrl($params = []) {
    $currentParams = $_GET;
    $newParams = array_merge($currentParams, $params);
    return '?' . http_build_query($newParams);
}
$orders_active = [];
$total_act_data = 0;
$total_act_pages = 1;
if ($role !== 'CUSTOMER') {
    $where_act = "WHERE o.status IN ('pending', 'processing', 'ready') OR (o.payment_status = 'unpaid' AND o.status != 'cancelled' AND o.status != 'completed')";
    $sql_count_act = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.customer_id = u.id $where_act";
    $total_act_data = ambil_satu_data($sql_count_act)['total'];
    if ($limit_act_val == 'all') {
        $limit_act = ($total_act_data > 0) ? $total_act_data : 1;
        $page_act = 1;
    } else {
        $limit_act = (int)$limit_act_val;
    }
    $total_act_pages = ceil($total_act_data / $limit_act);
    if ($page_act > $total_act_pages) $page_act = $total_act_pages;
    if ($page_act < 1) $page_act = 1;
    $offset_act = ($page_act - 1) * $limit_act;
    $sql_active = "SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.customer_id = u.id $where_act ORDER BY o.created_at ASC LIMIT $limit_act OFFSET $offset_act";
    $orders_active = ambil_banyak_data($sql_active);
}
$where_hist = "WHERE 1=1";
if ($role === 'CUSTOMER') {
    $where_hist .= " AND o.customer_id = $id_user";
} else {
    if (empty($search) && empty($start_date) && empty($filter_status)) {
        $where_hist .= " AND o.status IN ('completed', 'cancelled')";
    }
}
if (!empty($search)) $where_hist .= " AND (o.order_code LIKE '%$search%' OR o.pickup_code LIKE '%$search%' OR u.full_name LIKE '%$search%')";
if (!empty($start_date)) $where_hist .= " AND o.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
if (!empty($filter_status)) $where_hist .= " AND o.status = '$filter_status'";
$sql_count_hist = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.customer_id = u.id $where_hist";
$total_hist_data = ambil_satu_data($sql_count_hist)['total'];
if ($limit_hist_val == 'all') {
    $limit_hist = ($total_hist_data > 0) ? $total_hist_data : 1;
    $page_hist = 1;
} else {
    $limit_hist = (int)$limit_hist_val;
}
$total_hist_pages = ceil($total_hist_data / $limit_hist);
if ($page_hist > $total_hist_pages) $page_hist = $total_hist_pages;
if ($page_hist < 1) $page_hist = 1;
$offset_hist = ($page_hist - 1) * $limit_hist;
$sql_history = "SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.customer_id = u.id $where_hist ORDER BY o.created_at DESC LIMIT $limit_hist OFFSET $offset_hist";
$orders_history = ambil_banyak_data($sql_history);
if ($role === 'CUSTOMER') {
    $stats = ambil_satu_data("SELECT SUM(total_amount) as total_spent, COUNT(*) as total_trx, SUM(CASE WHEN payment_status='unpaid' THEN 1 ELSE 0 END) as unpaid FROM orders WHERE customer_id=$id_user");
    $c_stats = ['unpaid_bills' => $stats['unpaid'] ?? 0, 'spent_this_month' => $stats['total_spent'] ?? 0, 'total_orders' => $stats['total_trx'] ?? 0]; 
    $last_order = ambil_satu_data("SELECT * FROM orders WHERE customer_id = $id_user ORDER BY created_at DESC LIMIT 1");
} else {
    $finance = ambil_satu_data("SELECT SUM(CASE WHEN DATE(created_at)=CURDATE() THEN total_amount ELSE 0 END) as omset_today, SUM(CASE WHEN MONTH(created_at)=MONTH(CURDATE()) THEN total_amount ELSE 0 END) as omset_month FROM orders");
    $prod = ambil_satu_data("SELECT SUM(CASE WHEN status='processing' THEN 1 ELSE 0 END) as processing, SUM(CASE WHEN payment_status='unpaid' THEN 1 ELSE 0 END) as unpaid FROM orders");
    $stock_alert = ambil_satu_data("SELECT COUNT(*) as alert_count FROM products WHERE current_stock <= min_stock");
    if($role === 'STAFF') {
        $s_stats = ambil_satu_data("SELECT SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as global_pending, SUM(CASE WHEN status='processing' THEN 1 ELSE 0 END) as global_processing, SUM(CASE WHEN status='completed' AND DATE(updated_at)=CURDATE() THEN 1 ELSE 0 END) as done_today FROM orders");
    }
}
function getHistoryOptions($conn, $order_id) {
    $sql = "SELECT username, role, description, created_at FROM system_logs WHERE target_id = '$order_id' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $opts = "";
    if (mysqli_num_rows($result) > 0) {
        while ($r = mysqli_fetch_assoc($result)) {
            $time = date('d/m H:i', strtotime($r['created_at']));
            $txt = "[$time] {$r['username']} ({$r['role']}): {$r['description']}";
            $short = (strlen($txt)>50) ? substr($txt,0,50)."..." : $txt;
            $opts .= "<option title='$txt'>$short</option>";
        }
    } else {
        $opts = "<option disabled selected>- Belum ada log -</option>";
    }
    return $opts;
}
function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'completed': return '<span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold border border-green-200 flex items-center gap-1 w-fit mx-auto"><i class="fa-solid fa-check-double"></i> Selesai</span>';
        case 'ready': return '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold border border-emerald-200 animate-pulse flex items-center gap-1 w-fit mx-auto"><i class="fa-solid fa-box-open"></i> Siap Ambil</span>';
        case 'processing': return '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold border border-blue-200 flex items-center gap-1 w-fit mx-auto"><i class="fa-solid fa-gears"></i> Proses</span>';
        case 'pending': return '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold border border-yellow-200 flex items-center gap-1 w-fit mx-auto"><i class="fa-regular fa-clock"></i> Antrian</span>';
        case 'cancelled': return '<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold border border-red-200 flex items-center gap-1 w-fit mx-auto"><i class="fa-solid fa-xmark"></i> Batal</span>';
        default: return '<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-bold">-</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NPC System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            aside, header, .no-print, .btn-action { display: none !important; }
            main { margin: 0 !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
            body { background: white !important; font-size: 11px; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-header { display: block !important; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
            .card-print { border: 1px solid #ccc !important; box-shadow: none !important; }
            .table-print { width: 100%; border-collapse: collapse; }
            .table-print th, .table-print td { border: 1px solid #999; padding: 6px; }
            .table-print th { background-color: #eee !important; font-weight: bold; }
            .col-pj { display: none; }
        }
        .print-header { display: none; }
    </style>
</head>
<body class="text-slate-800 font-[Inter] bg-slate-50">
    <?php include '../../sidebar/sidebar.php'; ?>
    <?php include '../../header/header.php'; ?>
    <main class="p-4 md:p-8 md:ml-64 transition-all min-h-screen">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 no-print">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    <?php echo ($role === 'CUSTOMER') ? 'Dashboard Pelanggan' : 'Dashboard Operasional'; ?>
                </h1>
                <p class="text-slate-500 text-sm">Selamat bekerja, <b><?php echo htmlspecialchars($full_name); ?></b>.</p>
            </div>
            <div class="flex gap-2">
                <?php if($role !== 'OWNER'): ?>
                <a href="../order/catalog.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition text-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Order Baru
                </a>
                <?php endif; ?>
                <button onclick="window.print()" class="bg-white border border-slate-300 text-slate-700 px-4 py-2 rounded-lg font-medium shadow-sm transition text-sm flex items-center gap-2">
                    <i class="fa-solid fa-print"></i> Cetak Laporan
                </button>
            </div>
        </div>
        <?php if(!empty($pesan_aksi)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6 animate-pulse shadow-sm">
            <i class="fa-solid fa-check-circle mr-2"></i> <span class="font-medium"><?php echo $pesan_aksi; ?></span>
        </div>
        <?php endif; ?>
        <div class="print-header">
            <div class="flex justify-between items-end">
                <div>
                    <h1 class="text-xl font-bold uppercase">Laporan Data Pesanan</h1>
                    <p class="text-sm">NPC System - Nagoya Print & Copy</p>
                </div>
                <div class="text-right text-xs">
                    <p>Dicetak Oleh: <strong><?php echo $full_name; ?></strong></p>
                    <p>Waktu Cetak: <?php echo date('d/m/Y H:i'); ?></p>
                </div>
            </div>
            <div class="mt-4 pt-2 border-t border-dashed border-gray-400 text-xs flex gap-6">
                <span><strong>Filter Periode:</strong> <?php echo (!empty($start_date) ? date('d/m/Y', strtotime($start_date)).' s/d '.date('d/m/Y', strtotime($end_date)) : 'Semua Waktu'); ?></span>
                <span><strong>Filter Status:</strong> <?php echo (!empty($filter_status) ? strtoupper($filter_status) : 'Semua'); ?></span>
            </div>
        </div>
        <div class="no-print">
            <?php if($role === 'CUSTOMER'): ?>
                <?php if($c_stats['unpaid_bills'] > 0): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg flex items-start gap-3">
                    <i class="fa-solid fa-bell text-red-500 mt-1"></i>
                    <div>
                        <h3 class="font-bold text-red-800">Tagihan Belum Dibayar</h3>
                        <p class="text-sm text-red-600">Ada <strong><?php echo $c_stats['unpaid_bills']; ?></strong> pesanan menunggu pembayaran.</p>
                    </div>
                </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-5 bg-gradient-to-r from-slate-800 to-slate-900 text-white flex justify-between items-center">
                            <div>
                                <p class="text-xs text-slate-300 uppercase font-bold tracking-wider">Status Terkini</p>
                                <h3 class="font-bold text-lg mt-1">Order Terakhir</h3>
                            </div>
                            <?php if($last_order): ?><i class="fa-solid fa-clock-rotate-left text-2xl opacity-20"></i><?php endif; ?>
                        </div>
                        <div class="p-6">
                            <?php if($last_order): ?>
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <p class="text-xs text-slate-400">Kode Pickup</p>
                                        <span class="text-lg font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded"><?php echo $last_order['pickup_code']; ?></span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-400">Total</p>
                                        <span class="font-bold text-slate-800"><?php echo format_rupiah($last_order['total_amount']); ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-slate-500">Status:</span>
                                    <?php echo getStatusBadge($last_order['status']); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-slate-400 py-2">Belum ada riwayat pesanan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="bg-green-50 p-3 rounded-full text-green-600"><i class="fa-solid fa-wallet text-xl"></i></div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-bold">Pengeluaran Bulan Ini</p>
                                <h3 class="text-2xl font-bold text-slate-800"><?php echo format_rupiah($c_stats['spent_this_month']); ?></h3>
                            </div>
                        </div>
                        <div class="text-sm text-slate-500 border-t pt-4">
                            Total pesanan aktif: <strong><?php echo $c_stats['total_orders']; ?></strong>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if(isset($stock_alert) && $stock_alert['alert_count'] > 0): ?>
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg mb-6 flex justify-between items-center shadow-sm">
                    <div class="flex items-center gap-3"><i class="fa-solid fa-triangle-exclamation text-yellow-600"></i> <span class="font-medium">Perhatian: Ada <?php echo $stock_alert['alert_count']; ?> produk stok menipis!</span></div>
                    <a href="products.php" class="text-sm bg-white border border-yellow-300 px-3 py-1 rounded hover:bg-yellow-100 transition">Cek Sekarang</a>
                </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <?php if($role === 'STAFF'): ?>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Antrian Baru</p>
                            <h3 class="text-2xl font-bold text-yellow-600"><?php echo $s_stats['global_pending']; ?></h3>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Sedang Jalan</p>
                            <h3 class="text-2xl font-bold text-blue-600"><?php echo $s_stats['global_processing']; ?></h3>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Selesai Hari Ini</p>
                            <h3 class="text-2xl font-bold text-green-600"><?php echo $s_stats['done_today']; ?></h3>
                        </div>
                    <?php else: ?>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Omset Hari Ini</p>
                            <h3 class="text-2xl font-bold text-slate-800"><?php echo format_rupiah($finance['omset_today']); ?></h3>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Omset Bulan Ini</p>
                            <h3 class="text-2xl font-bold text-slate-800"><?php echo format_rupiah($finance['omset_month']); ?></h3>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Sedang Diproses</p>
                            <h3 class="text-2xl font-bold text-blue-600"><?php echo $prod['processing']; ?></h3>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Tagihan Unpaid</p>
                            <h3 class="text-2xl font-bold text-red-600"><?php echo $prod['unpaid']; ?></h3>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($role !== 'CUSTOMER'): ?>
        <div class="bg-white rounded-xl shadow-sm border border-blue-200 overflow-hidden mb-8 no-print card-print">
            <div class="p-5 border-b border-blue-100 bg-blue-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-lg text-blue-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-blue-600"></i> Antrian Pesanan Aktif
                    </h3>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full font-bold border border-blue-200">
                        Total: <?php echo $total_act_data; ?>
                    </span>
                </div>
                <div class="flex items-center gap-2 text-sm text-slate-600">
                    <span>Tampilkan:</span>
                    <select onchange="window.location.href='<?php echo buildUrl(['page_act'=>1, 'limit_act'=>'']); ?>' + this.value" class="border border-slate-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                        <option value="5" <?php if($limit_act_val == 5) echo 'selected'; ?>>5 Baris</option>
                        <option value="10" <?php if($limit_act_val == 10) echo 'selected'; ?>>10 Baris</option>
                        <option value="20" <?php if($limit_act_val == 20) echo 'selected'; ?>>20 Baris</option>
                        <option value="all" <?php if($limit_act_val == 'all') echo 'selected'; ?>>Semua</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table-print">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Order ID</th>
                            <th class="px-6 py-3">Pelanggan</th>
                            <th class="px-6 py-3 text-right">Nominal</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3 text-center w-64 col-pj">Riwayat & PJ</th>
                            <th class="px-6 py-3 text-center w-64 btn-action">Tindakan Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (count($orders_active) > 0): ?>
                            <?php foreach ($orders_active as $row): ?>
                            <tr class="hover:bg-blue-50/10 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-slate-700"><?php echo $row['order_code']; ?></span>
                                    <div class="text-[10px] text-slate-400 mt-1">Pick: <span class="font-bold"><?php echo $row['pickup_code']; ?></span></div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    <?php echo $row['customer_name']; ?>
                                    <div class="text-xs text-slate-400"><?php echo date('d/m H:i', strtotime($row['created_at'])); ?></div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-700">
                                    <?php echo format_rupiah($row['total_amount']); ?>
                                    <div class="text-[10px] mt-1 <?php echo ($row['payment_status']=='paid') ? 'text-green-600' : 'text-red-500'; ?> uppercase font-bold">
                                        <?php echo $row['payment_status']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center"><?php echo getStatusBadge($row['status']); ?></td>
                                <td class="px-6 py-4 col-pj">
                                    <select class="w-full text-xs border border-slate-300 rounded px-2 py-1 bg-slate-50 text-slate-600 focus:outline-none" title="Klik untuk lihat riwayat perubahan">
                                        <?php echo getHistoryOptions($conn, $row['id']); ?>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-center btn-action">
                                    <div class="flex justify-center gap-2 items-center">
                                        <?php if($row['status'] == 'pending'): ?>
                                            <form method="POST" onsubmit="return confirm('Mulai proses pesanan ini?');">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="current_status" value="pending">
                                                <button type="submit" name="act_next_stage" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded shadow flex items-center gap-1 transition"><i class="fa-solid fa-play"></i> Proses</button>
                                            </form>
                                        <?php elseif($row['status'] == 'processing'): ?>
                                            <form method="POST" onsubmit="return confirm('Produksi selesai?');">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="current_status" value="processing">
                                                <button type="submit" name="act_next_stage" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1.5 rounded shadow flex items-center gap-1 transition"><i class="fa-solid fa-check"></i> Selesai Produksi</button>
                                            </form>
                                        <?php elseif($row['status'] == 'ready'): ?>
                                            <form method="POST" onsubmit="return confirm('Barang diserahkan?');">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <input type="hidden" name="current_status" value="ready">
                                                <button type="submit" name="act_next_stage" class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded shadow flex items-center gap-1 transition"><i class="fa-solid fa-handshake"></i> Serahkan</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if($row['payment_status'] == 'unpaid'): ?>
                                            <form method="POST" onsubmit="return confirm('Terima pembayaran tunai?');">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="act_pay" class="text-green-600 hover:bg-green-50 px-2 py-1 rounded border border-green-200 transition" title="Bayar"><i class="fa-solid fa-money-bill-wave"></i></button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="text-slate-400 hover:text-blue-600 px-2" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-8 text-slate-400 bg-slate-50 italic">Tidak ada pesanan aktif.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($total_act_data > 0 && $limit_act_val != 'all'): ?>
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center bg-slate-50 gap-3 no-print text-xs">
                <span class="text-slate-500">
                    Menampilkan <strong><?php echo $offset_act + 1; ?></strong> - <strong><?php echo min($offset_act + $limit_act, $total_act_data); ?></strong> dari <strong><?php echo $total_act_data; ?></strong> data
                </span>
                <div class="flex gap-1">
                    <a href="<?php echo ($page_act > 1) ? buildUrl(['page_act' => $page_act - 1]) : '#'; ?>" 
                       class="<?php echo ($page_act > 1) ? 'bg-white hover:bg-blue-50 text-slate-700' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?> px-3 py-1.5 border rounded transition flex items-center gap-1">
                        <i class="fa-solid fa-chevron-left"></i> Prev
                    </a>
                    <?php for($i=1; $i<=$total_act_pages; $i++): ?>
                        <?php if($i == 1 || $i == $total_act_pages || ($i >= $page_act - 1 && $i <= $page_act + 1)): ?>
                            <a href="<?php echo buildUrl(['page_act' => $i]); ?>" class="px-3 py-1.5 border rounded <?php echo ($i == $page_act) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white hover:bg-slate-100 text-slate-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif($i == $page_act - 2 || $i == $page_act + 2): ?>
                            <span class="px-2 py-1.5 text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <a href="<?php echo ($page_act < $total_act_pages) ? buildUrl(['page_act' => $page_act + 1]) : '#'; ?>" 
                       class="<?php echo ($page_act < $total_act_pages) ? 'bg-white hover:bg-blue-50 text-slate-700' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?> px-3 py-1.5 border rounded transition flex items-center gap-1">
                        Next <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden card-print">
            <div class="p-5 border-b border-slate-100 bg-slate-50 no-print">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> 
                        <?php echo ($role !== 'CUSTOMER') ? 'Arsip Riwayat Pesanan' : 'Riwayat Pesanan Saya'; ?>
                    </h3>
                     <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span>Tampilkan:</span>
                        <select onchange="window.location.href='<?php echo buildUrl(['page_hist'=>1, 'limit_hist'=>'']); ?>' + this.value" class="border border-slate-300 rounded px-2 py-1 bg-white focus:outline-none focus:ring-1 focus:ring-slate-500 text-xs">
                            <option value="5" <?php if($limit_hist_val == 5) echo 'selected'; ?>>5 Baris</option>
                            <option value="10" <?php if($limit_hist_val == 10) echo 'selected'; ?>>10 Baris</option>
                            <option value="20" <?php if($limit_hist_val == 20) echo 'selected'; ?>>20 Baris</option>
                            <option value="all" <?php if($limit_hist_val == 'all') echo 'selected'; ?>>Semua</option>
                        </select>
                    </div>
                </div>
                <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <input type="hidden" name="limit_act" value="<?php echo $limit_act_val; ?>">
                    <input type="hidden" name="limit_hist" value="<?php echo $limit_hist_val; ?>">
                    <div class="md:col-span-12 lg:col-span-4 relative">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Kode..." class="w-full pl-9 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-slate-500 outline-none">
                        <i class="fa-solid fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                    <div class="md:col-span-6 lg:col-span-2">
                        <label class="block text-[10px] text-slate-500 font-bold mb-1">DARI</label>
                        <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                    </div>
                    <div class="md:col-span-6 lg:col-span-2">
                        <label class="block text-[10px] text-slate-500 font-bold mb-1">SAMPAI</label>
                        <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="w-full px-3 py-2 border rounded-lg text-sm outline-none">
                    </div>
                    <?php if($role !== 'CUSTOMER'): ?>
                    <div class="md:col-span-6 lg:col-span-2">
                        <label class="block text-[10px] text-slate-500 font-bold mb-1">STATUS</label>
                        <select name="filter_status" class="w-full px-3 py-2 border rounded-lg text-sm bg-white">
                            <option value="">Semua</option>
                            <option value="completed" <?php if($filter_status=='completed') echo 'selected'; ?>>Selesai</option>
                            <option value="cancelled" <?php if($filter_status=='cancelled') echo 'selected'; ?>>Batal</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    <div class="md:col-span-6 lg:col-span-2">
                        <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm w-full font-bold">Filter</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table-print">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Order Info</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <?php if($role !== 'CUSTOMER'): ?><th class="px-6 py-4">Customer</th><?php endif; ?>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center w-64 col-pj">Riwayat & PJ</th>
                            <th class="px-6 py-4 text-center btn-action">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (count($orders_history) > 0): ?>
                            <?php foreach ($orders_history as $row): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-slate-700"><?php echo $row['order_code']; ?></span>
                                </td>
                                <td class="px-6 py-4 text-slate-600"><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                <?php if($role !== 'CUSTOMER'): ?>
                                <td class="px-6 py-4 font-medium text-slate-700"><?php echo $row['customer_name']; ?></td>
                                <?php endif; ?>
                                <td class="px-6 py-4 text-right font-bold text-slate-700">
                                    <?php echo format_rupiah($row['total_amount']); ?>
                                </td>
                                <td class="px-6 py-4 text-center"><?php echo getStatusBadge($row['status']); ?></td>
                                <td class="px-6 py-4 col-pj">
                                    <select class="w-full text-xs border border-slate-300 rounded px-2 py-1 bg-slate-50 text-slate-600 focus:outline-none">
                                        <?php echo getHistoryOptions($conn, $row['id']); ?>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-center btn-action">
                                    <div class="flex justify-center gap-2">
                                        <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="text-slate-400 hover:text-blue-600 p-2" title="Detail"><i class="fa-solid fa-eye"></i></a>
                                        <?php if(($role == 'ADMIN' || $role == 'OWNER') && !in_array($row['status'], ['completed', 'cancelled'])): ?>
                                            <button onclick="bukaModalBatal(<?php echo $row['id']; ?>)" class="text-slate-400 hover:text-red-600 p-2" title="Batalkan"><i class="fa-solid fa-ban"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-12 text-slate-400 italic">Data riwayat tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($limit_hist_val != 'all' && $total_hist_data > 0): ?>
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center bg-slate-50 gap-3 no-print text-xs">
                <span class="text-slate-500">
                    Menampilkan <strong><?php echo $offset_hist + 1; ?></strong> - <strong><?php echo min($offset_hist + $limit_hist, $total_hist_data); ?></strong> dari <strong><?php echo $total_hist_data; ?></strong> data
                </span>
                <div class="flex gap-1">
                    <a href="<?php echo ($page_hist > 1) ? buildUrl(['page_hist' => $page_hist - 1]) : '#'; ?>" 
                       class="<?php echo ($page_hist > 1) ? 'bg-white hover:bg-slate-100 text-slate-700' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?> px-3 py-1.5 border rounded transition flex items-center gap-1">
                        <i class="fa-solid fa-chevron-left"></i> Prev
                    </a>
                    <?php for($i=1; $i<=$total_hist_pages; $i++): ?>
                        <?php if($i == 1 || $i == $total_hist_pages || ($i >= $page_hist - 1 && $i <= $page_hist + 1)): ?>
                            <a href="<?php echo buildUrl(['page_hist' => $i]); ?>" class="px-3 py-1.5 border rounded <?php echo ($i == $page_hist) ? 'bg-slate-800 text-white border-slate-800' : 'bg-white hover:bg-slate-100 text-slate-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif($i == $page_hist - 2 || $i == $page_hist + 2): ?>
                            <span class="px-2 py-1.5 text-slate-400">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <a href="<?php echo ($page_hist < $total_hist_pages) ? buildUrl(['page_hist' => $page_hist + 1]) : '#'; ?>" 
                       class="<?php echo ($page_hist < $total_hist_pages) ? 'bg-white hover:bg-slate-100 text-slate-700' : 'bg-slate-100 text-slate-400 cursor-not-allowed'; ?> px-3 py-1.5 border rounded transition flex items-center gap-1">
                        Next <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    <div id="modalBatal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center no-print">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm mx-4">
            <h3 class="font-bold text-lg mb-2">Batalkan Pesanan?</h3>
            <form method="POST">
                <input type="hidden" name="order_id" id="cancel_order_id">
                <input type="hidden" name="act_cancel" value="1">
                <textarea name="reason" class="w-full border rounded p-2 text-sm mb-4" rows="3" placeholder="Alasan..." required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalBatal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 rounded text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded text-sm">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
    <script>function bukaModalBatal(id){document.getElementById('cancel_order_id').value=id;document.getElementById('modalBatal').classList.remove('hidden');}</script>
</body>
</html>