<?php
session_start();
require_once '../../koneksi/database.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../login/login.php');
    exit;
}

$role = strtoupper($_SESSION['user']['role']);
$allowed_roles = ['ADMIN', 'OWNER', 'STAFF', 'CUSTOMER'];
if (!in_array($role, $allowed_roles, true)) {
    header('Location: ../dashboard/dashboard.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: ../dashboard/dashboard.php');
    exit;
}

$whereClause = "WHERE o.id = $orderId";
if ($role === 'CUSTOMER') {
    $customerId = (int)$_SESSION['user']['id'];
    $whereClause .= " AND o.customer_id = $customerId";
}

$orderQuery = "SELECT o.*, 
                      cust.full_name AS customer_name,
                      cust.email AS customer_email,
                      cust.phone AS customer_phone,
                      staff.full_name AS staff_name
               FROM orders o
               JOIN users cust ON o.customer_id = cust.id
               LEFT JOIN users staff ON o.staff_id = staff.id
               $whereClause
               LIMIT 1";
$order = ambil_satu_data($orderQuery);

if (!$order) {
    header('Location: ../dashboard/dashboard.php');
    exit;
}

$order_items = ambil_banyak_data("SELECT oi.*, 
        COALESCE(s.name, p.name) AS item_name,
        COALESCE(s.code, p.code) AS item_code,
        COALESCE(s.unit, p.unit, 'unit') AS unit_label
    FROM order_items oi
    LEFT JOIN services s ON oi.service_id = s.id
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = {$order['id']}
    ORDER BY oi.id ASC");

$transactions = ambil_banyak_data("SELECT * FROM order_payment_logs WHERE order_id = {$order['id']} ORDER BY created_at DESC");

$status_labels = [
    'pending' => ['bg-amber-100 text-amber-700 border border-amber-200', 'Menunggu Diproses'],
    'processing' => ['bg-blue-100 text-blue-700 border border-blue-200', 'Sedang Diproduksi'],
    'ready' => ['bg-emerald-100 text-emerald-700 border border-emerald-200', 'Siap Diambil'],
    'completed' => ['bg-slate-900 text-white border border-slate-900', 'Selesai'],
    'cancelled' => ['bg-rose-100 text-rose-700 border border-rose-200', 'Dibatalkan'],
];

$payment_labels = [
    'unpaid' => ['text-rose-600 bg-rose-50 border border-rose-200', 'Belum Bayar'],
    'partial' => ['text-amber-600 bg-amber-50 border border-amber-200', 'Bayar Sebagian'],
    'paid' => ['text-emerald-600 bg-emerald-50 border border-emerald-200', 'Lunas (Menunggu Verifikasi)'],
    'verified' => ['text-emerald-700 bg-emerald-100 border border-emerald-200', 'Lunas Terverifikasi'],
];

$remaining_amount = max(0, $order['total_amount'] - $order['paid_amount']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Order - <?php echo htmlspecialchars($order['order_code']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255,255,255,0.9); backdrop-filter: blur(16px); }
    </style>
</head>
<body class="bg-slate-100 text-slate-800">
    <?php include '../../sidebar/sidebar.php'; ?>
    <?php include '../../header/header.php'; ?>

    <main class="md:ml-64 min-h-screen p-6 md:p-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs uppercase text-slate-400 font-semibold tracking-[0.3em]">Order Detail</p>
                <h1 class="text-3xl font-extrabold text-slate-900 mt-1 flex items-center gap-3">
                    <?php echo htmlspecialchars($order['order_code']); ?>
                    <span class="text-sm px-3 py-1 rounded-full <?php echo $status_labels[strtolower($order['status'])][0] ?? 'bg-slate-200 text-slate-600'; ?>">
                        <?php echo $status_labels[strtolower($order['status'])][1] ?? ucfirst($order['status']); ?>
                    </span>
                </h1>
                <p class="text-slate-500">Pickup Code: <strong><?php echo htmlspecialchars($order['pickup_code']); ?></strong></p>
            </div>
            <div class="flex flex-wrap gap-2 text-sm font-semibold">
                <a href="dashboard.php" class="px-4 py-2 rounded-full border border-slate-300 text-slate-600 hover:bg-white">← Kembali ke Dashboard</a>
                <?php if ($role !== 'CUSTOMER'): ?>
                <a href="dashboard.php?view=orders" class="px-4 py-2 rounded-full border border-slate-300 text-slate-600 hover:bg-white">Daftar Order</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4 mb-8">
            <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm">
                <p class="text-xs uppercase text-slate-400 font-semibold">Status Pembayaran</p>
                <div class="mt-2 text-lg font-bold text-slate-900 flex items-center gap-3">
                    <?php echo format_rupiah($order['paid_amount']); ?>
                    <span class="text-xs px-3 py-1 rounded-full <?php echo $payment_labels[strtolower($order['payment_status'])][0] ?? 'bg-slate-100 text-slate-600'; ?>">
                        <?php echo $payment_labels[strtolower($order['payment_status'])][1] ?? ucfirst($order['payment_status']); ?>
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Total Tagihan: <strong><?php echo format_rupiah($order['total_amount']); ?></strong></p>
            </div>
            <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm">
                <p class="text-xs uppercase text-slate-400 font-semibold">Sisa Pembayaran</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-1"><?php echo format_rupiah($remaining_amount); ?></h3>
                <p class="text-xs text-slate-400 mt-1">Terakhir diperbarui: <?php echo date('d M Y, H:i', strtotime($order['updated_at'])); ?></p>
            </div>
            <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm">
                <p class="text-xs uppercase text-slate-400 font-semibold">Waktu Dibuat</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></h3>
                <p class="text-xs text-slate-400 mt-1">Oleh pelanggan: <?php echo htmlspecialchars($order['customer_name']); ?></p>
            </div>
            <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm">
                <p class="text-xs uppercase text-slate-400 font-semibold">Penanggung Jawab</p>
                <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($order['staff_name'] ?? 'Belum ditugaskan'); ?></h3>
                <p class="text-xs text-slate-400 mt-1">Pickup Code: <?php echo htmlspecialchars($order['pickup_code']); ?></p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3 mb-8">
            <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase text-slate-400 font-semibold">Informasi Pemesan</p>
                        <h2 class="text-2xl font-bold text-slate-900">Profil Customer</h2>
                    </div>
                    <div class="text-right text-xs text-slate-400">
                        <p>Order ID: <strong>#<?php echo $order['id']; ?></strong></p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs uppercase text-slate-400">Nama Pelanggan</p>
                        <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p class="text-sm text-slate-500 mt-1">Email: <?php echo htmlspecialchars($order['customer_email'] ?? '-'); ?></p>
                        <p class="text-sm text-slate-500">Telepon: <?php echo htmlspecialchars($order['customer_phone'] ?? '-'); ?></p>
                    </div>
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                        <p class="text-xs uppercase text-slate-400">Catatan / Instruksi</p>
                        <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                            <?php echo $order['notes'] ? nl2br(htmlspecialchars($order['notes'])) : '<span class="text-slate-400">Belum ada catatan khusus.</span>'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <p class="text-xs uppercase text-slate-400 font-semibold">Ringkasan Keuangan</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><span>Total Tagihan</span><strong><?php echo format_rupiah($order['total_amount']); ?></strong></div>
                    <div class="flex justify-between"><span>Total Dibayar</span><strong><?php echo format_rupiah($order['paid_amount']); ?></strong></div>
                    <div class="flex justify-between"><span>Sisa Pembayaran</span><strong class="text-rose-600"><?php echo format_rupiah($remaining_amount); ?></strong></div>
                    <div class="flex justify-between"><span>Status Pembayaran</span><strong><?php echo ucfirst($order['payment_status']); ?></strong></div>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase text-slate-400 font-semibold mb-2">Riwayat Transaksi</p>
                    <?php if (empty($transactions)): ?>
                        <div class="text-sm text-slate-400 italic">Belum ada transaksi tercatat.</div>
                    <?php else: ?>
                        <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                            <?php foreach ($transactions as $trx): ?>
                                <div class="border border-slate-100 rounded-2xl px-4 py-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($trx['transaction_code']); ?></span>
                                        <span class="text-xs uppercase tracking-wide text-slate-400"><?php echo date('d M Y H:i', strtotime($trx['created_at'])); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-slate-100 text-slate-600"><?php echo strtoupper($trx['method']); ?></span>
                                        <span class="font-semibold"><?php echo format_rupiah($trx['amount']); ?></span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                                        <span>Status: <?php echo ucfirst($trx['status']); ?></span>
                                        <?php if (!empty($trx['proof_image'])):
                                            $proofPath = ltrim($trx['proof_image'], '/');
                                            if (strpos($proofPath, 'src/') === 0) {
                                                $proofPath = substr($proofPath, 4);
                                            }
                                            $proofToken = urlencode(base64_encode($proofPath));
                                        ?>
                                            <a href="../order/proof_viewer.php?token=<?php echo $proofToken; ?>" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                <div>
                    <p class="text-xs uppercase text-slate-400 font-semibold">Item Pesanan</p>
                    <h2 class="text-2xl font-bold text-slate-900">Daftar Produk & Layanan</h2>
                </div>
                <span class="text-sm text-slate-500">Total Item: <strong><?php echo count($order_items); ?></strong></span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Harga Satuan</th>
                            <th class="px-4 py-3">Subtotal</th>
                            <th class="px-4 py-3">Catatan</th>
                            <th class="px-4 py-3">File</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($order_items)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-slate-400 py-6">Belum ada item terdaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($order_items as $item): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-slate-900"><?php echo htmlspecialchars($item['item_name'] ?? 'Item'); ?></p>
                                        <p class="text-xs text-slate-400">Kode: <?php echo htmlspecialchars($item['item_code'] ?? '-'); ?></p>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full <?php echo $item['item_type'] === 'service' ? 'bg-blue-50 text-blue-600' : 'bg-orange-50 text-orange-600'; ?>">
                                            <?php echo strtoupper($item['item_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-semibold">
                                        <?php echo rtrim(rtrim(number_format($item['quantity'], 2, ',', '.'), '0'), ','); ?>
                                        <span class="text-xs text-slate-400"><?php echo htmlspecialchars($item['unit_label']); ?></span>
                                    </td>
                                    <td class="px-4 py-4"><?php echo format_rupiah($item['unit_price']); ?></td>
                                    <td class="px-4 py-4 font-semibold text-slate-900"><?php echo format_rupiah($item['subtotal']); ?></td>
                                    <td class="px-4 py-4 text-xs text-slate-500">
                                        <?php echo $item['specifications'] ? nl2br(htmlspecialchars($item['specifications'])) : '<span class="text-slate-400">-</span>'; ?>
                                    </td>
                                    <td class="px-4 py-4 text-xs">
                                        <?php if ($item['upload_type'] === 'file' && !empty($item['file_path'])):
                                            $relativePath = ltrim((string)$item['file_path'], '/');
                                            $fileToken = urlencode(base64_encode($relativePath));
                                            $fileNameLabel = $item['file_name'] ?? basename($relativePath);
                                        ?>
                                            <div class="space-y-2">
                                                <p class="text-slate-500 break-words">
                                                    <i class="fa-solid fa-paperclip text-slate-400"></i>
                                                    <?php echo htmlspecialchars($fileNameLabel); ?>
                                                </p>
                                                <div class="flex flex-wrap gap-2">
                                                    <a href="../order/file_viewer.php?token=<?php echo $fileToken; ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-900 text-white rounded-full text-xs font-semibold hover:bg-slate-800">
                                                        <i class="fa-solid fa-eye"></i> Preview
                                                    </a>
                                                    <a href="../order/file_viewer.php?token=<?php echo $fileToken; ?>&download=1" class="inline-flex items-center gap-1 px-3 py-1 border border-slate-300 text-slate-700 rounded-full text-xs font-semibold hover:bg-white">
                                                        <i class="fa-solid fa-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        <?php elseif ($item['upload_type'] === 'link' && !empty($item['file_link'])): ?>
                                            <a href="<?php echo htmlspecialchars($item['file_link']); ?>" target="_blank" class="text-blue-600 hover:underline">Lihat Link</a>
                                        <?php else: ?>
                                            <span class="text-slate-400">Tidak ada</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <p class="text-xs uppercase text-slate-400 font-semibold mb-3">Status Produksi</p>
                <div class="flex flex-wrap gap-3">
                    <?php
                        $stages = ['pending' => 'Order Masuk', 'processing' => 'Produksi', 'ready' => 'Siap Ambil', 'completed' => 'Selesai'];
                        $stageKeys = array_keys($stages);
                        $currentIndex = array_search(strtolower($order['status']), $stageKeys, true);
                        if ($currentIndex === false) { $currentIndex = -1; }
                        $index = 0;
                        foreach ($stages as $key => $label):
                            $stepIndex = array_search($key, $stageKeys, true);
                            $active = ($stepIndex !== false) && ($stepIndex <= $currentIndex);
                    ?>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold <?php echo $active ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-500'; ?>"><?php echo ++$index; ?></span>
                            <span class="text-sm font-semibold <?php echo $active ? 'text-slate-900' : 'text-slate-400'; ?>"><?php echo $label; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <p class="text-xs uppercase text-slate-400 font-semibold mb-3">Catatan Sistem</p>
                <?php
                    $logs = ambil_banyak_data("SELECT username, role, description, created_at FROM system_logs WHERE target_id = {$order['id']} ORDER BY created_at DESC LIMIT 5");
                ?>
                <?php if (empty($logs)): ?>
                    <p class="text-sm text-slate-400">Belum ada aktivitas yang tercatat.</p>
                <?php else: ?>
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        <?php foreach ($logs as $log): ?>
                            <div class="border border-slate-100 rounded-2xl px-4 py-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($log['username']); ?></span>
                                    <span class="text-xs text-slate-400"><?php echo date('d M H:i', strtotime($log['created_at'])); ?></span>
                                </div>
                                <p class="text-xs text-slate-500 uppercase tracking-wide"><?php echo strtoupper($log['role']); ?></p>
                                <p class="mt-2 text-slate-600"><?php echo htmlspecialchars($log['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
