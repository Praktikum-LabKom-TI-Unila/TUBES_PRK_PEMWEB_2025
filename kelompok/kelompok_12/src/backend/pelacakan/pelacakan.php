<?php
session_start();
require_once '../../koneksi/database.php'; 

if (!function_exists('format_rupiah')) {
    function format_rupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

$pickup_code = isset($_GET['pickup_code']) ? trim(htmlspecialchars($_GET['pickup_code'])) : '';
$order_found = false;
$order_data = null;
$order_items = [];
$error_message = '';

if (!empty($pickup_code)) {
    $sql_order = "SELECT o.*, u.full_name as customer_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.pickup_code = ?";
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param("s", $pickup_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order_found = true;
        $order_data = $result->fetch_assoc();
        
        $sql_items = "SELECT oi.*, s.name as service_name, p.name as product_name, s.unit as service_unit, p.unit as product_unit FROM order_items oi LEFT JOIN services s ON oi.service_id = s.id LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        
        $stmt_items = $conn->prepare($sql_items);
        $stmt_items->bind_param("i", $order_data['id']);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        
        while($row = $result_items->fetch_assoc()) {
            $order_items[] = $row;
        }
    } else {
        $error_message = "Kode Pickup <b>" . htmlspecialchars($pickup_code) . "</b> tidak ditemukan.";
    }
}

function getStatusStep($current_status) {
    $statuses = ['pending', 'processing', 'ready', 'completed'];
    $key = array_search($current_status, $statuses);
    return ($key === false) ? -1 : $key;
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan - NPC System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { npcGreen: '#10B981', npcDark: '#0F172A', npcGray: '#F8FAFC' }
                }
            }
        }
    </script>
</head>
<body class="font-sans text-gray-600 bg-npcGray">
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-2 group text-gray-600 hover:text-npcGreen transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="font-bold">Kembali ke Beranda</span>
            </a>
            <div class="font-bold text-npcDark flex items-center gap-2">
                <div class="bg-npcGreen text-white px-2 py-0.5 rounded text-sm"><i class="fa-solid fa-print"></i></div>
                NPC Tracking
            </div>
        </div>
    </nav>
    <section class="bg-npcDark py-12 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-green-500 rounded-full mix-blend-screen filter blur-3xl"></div>
        </div>
        <div class="max-w-3xl mx-auto px-6 relative z-10 text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Lacak Status Pesanan</h1>
            <p class="text-gray-400 mb-8 text-sm">Masukkan kode pickup unik Anda untuk melihat progres pengerjaan.</p>
            <form action="" method="GET" class="bg-white/10 p-2 rounded-2xl border border-white/10 backdrop-blur-md flex flex-col sm:flex-row gap-2 shadow-2xl">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="pickup_code" value="<?php echo $pickup_code; ?>" placeholder="Masukkan Kode Pickup (Cth: TRX-12345)" class="w-full bg-transparent text-white pl-10 pr-4 py-3 rounded-xl focus:outline-none placeholder:text-gray-500 font-medium uppercase" required>
                </div>
                <button type="submit" class="bg-npcGreen px-8 py-3 rounded-xl font-bold text-white hover:bg-green-600 transition shadow-lg whitespace-nowrap">
                    <i class="fa-solid fa-crosshairs mr-2"></i> Lacak
                </button>
            </form>
            <?php if(!empty($error_message)): ?>
                <div class="mt-4 bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl text-sm flex items-center justify-center gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php if($order_found && $order_data): ?>
    <section class="max-w-5xl mx-auto px-6 -mt-8 relative z-20 pb-20">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 mb-8">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kode Pickup</span>
                    <h2 class="text-3xl font-extrabold text-npcDark tracking-tight"><?php echo strtoupper($order_data['pickup_code']); ?></h2>
                    <div class="text-sm text-gray-500 mt-1">
                        <i class="fa-regular fa-calendar mr-1"></i> Dipesan: <?php echo date('d M Y H:i', strtotime($order_data['created_at'])); ?>
                    </div>
                </div>
                <div class="text-right">
                    <?php 
                        $badge_color = 'bg-gray-100 text-gray-600';
                        $icon_status = 'fa-clock';
                        if($order_data['status'] == 'processing') { $badge_color = 'bg-blue-100 text-blue-700'; $icon_status = 'fa-gears'; }
                        elseif($order_data['status'] == 'ready') { $badge_color = 'bg-yellow-100 text-yellow-700'; $icon_status = 'fa-box-open'; }
                        elseif($order_data['status'] == 'completed') { $badge_color = 'bg-green-100 text-green-700'; $icon_status = 'fa-check-circle'; }
                        elseif($order_data['status'] == 'cancelled') { $badge_color = 'bg-red-100 text-red-700'; $icon_status = 'fa-ban'; }
                    ?>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm <?php echo $badge_color; ?>">
                        <i class="fa-solid <?php echo $icon_status; ?>"></i>
                        <?php echo strtoupper($order_data['status']); ?>
                    </div>
                    <div class="mt-2 text-sm">
                        Total: <span class="font-bold text-npcDark"><?php echo format_rupiah($order_data['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            <?php if($order_data['status'] != 'cancelled'): ?>
            <div class="p-8">
                <div class="relative">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 z-0 rounded"></div>
                    <?php 
                        $step = getStatusStep($order_data['status']); 
                        $width = ($step / 3) * 100;
                    ?>
                    <div class="absolute top-1/2 left-0 h-1 bg-npcGreen -translate-y-1/2 z-0 rounded transition-all duration-1000" style="width: <?php echo $width; ?>%;"></div>
                    <div class="relative z-10 flex justify-between w-full">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 <?php echo ($step >= 0) ? 'bg-npcGreen border-npcGreen text-white' : 'bg-white border-gray-300 text-gray-300'; ?>">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <span class="text-xs font-bold <?php echo ($step >= 0) ? 'text-npcGreen' : 'text-gray-400'; ?>">Diterima</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 <?php echo ($step >= 1) ? 'bg-npcGreen border-npcGreen text-white' : 'bg-white border-gray-300 text-gray-300'; ?>">
                                <i class="fa-solid fa-print"></i>
                            </div>
                            <span class="text-xs font-bold <?php echo ($step >= 1) ? 'text-npcGreen' : 'text-gray-400'; ?>">Diproses</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 <?php echo ($step >= 2) ? 'bg-npcGreen border-npcGreen text-white' : 'bg-white border-gray-300 text-gray-300'; ?>">
                                <i class="fa-solid fa-box"></i>
                            </div>
                            <span class="text-xs font-bold <?php echo ($step >= 2) ? 'text-npcGreen' : 'text-gray-400'; ?>">Siap Ambil</span>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 <?php echo ($step >= 3) ? 'bg-npcGreen border-npcGreen text-white' : 'bg-white border-gray-300 text-gray-300'; ?>">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="text-xs font-bold <?php echo ($step >= 3) ? 'text-npcGreen' : 'text-gray-400'; ?>">Selesai</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 bg-blue-50 border border-blue-100 p-4 rounded-xl flex gap-3 items-start">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
                    <div class="text-sm text-blue-800">
                        <strong>Informasi Status:</strong>
                        <?php if($order_data['status'] == 'pending'): ?>
                            Pesanan Anda telah masuk antrian. Mohon tunggu konfirmasi admin atau proses produksi dimulai.
                        <?php elseif($order_data['status'] == 'processing'): ?>
                            Pesanan sedang dikerjakan oleh mesin/staff kami. Estimasi waktu tergantung jumlah antrian.
                        <?php elseif($order_data['status'] == 'ready'): ?>
                            <span class="font-bold">Hore! Pesanan sudah siap.</span> Silakan datang ke outlet Nagoya Print & Copy untuk mengambil pesanan Anda. Jangan lupa bawa Kode Pickup ini.
                        <?php elseif($order_data['status'] == 'completed'): ?>
                            Pesanan telah diambil dan transaksi selesai. Terima kasih telah menggunakan jasa kami!
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500 text-2xl">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">Pesanan Dibatalkan</h3>
                    <p class="text-gray-500 mt-2">Maaf, pesanan ini telah dibatalkan oleh sistem atau admin. Hubungi CS untuk info lebih lanjut.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="font-bold text-npcDark text-lg mb-4 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-npcGreen"></i> Rincian Item
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="p-3 rounded-l-lg">Item</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">Harga Satuan</th>
                            <th class="p-3 text-right rounded-r-lg">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-gray-100">
                        <?php foreach($order_items as $item): ?>
                        <tr>
                            <td class="p-3 font-medium text-gray-700">
                                <?php 
                                    echo $item['item_type'] == 'service' ? $item['service_name'] : $item['product_name'];
                                ?>
                                <?php if(!empty($item['specifications'])): ?>
                                    <div class="text-xs text-gray-400 font-normal mt-1">
                                        Spek: <?php echo $item['specifications']; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-3">
                                <?php echo $item['quantity']; ?> 
                                <span class="text-xs text-gray-400">
                                    <?php echo strtolower($item['item_type'] == 'service' ? $item['service_unit'] : $item['product_unit']); ?>
                                </span>
                            </td>
                            <td class="p-3 text-gray-500"><?php echo format_rupiah($item['unit_price']); ?></td>
                            <td class="p-3 text-right font-bold text-gray-700"><?php echo format_rupiah($item['subtotal']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="border-t border-gray-100">
                        <tr>
                            <td colspan="3" class="p-3 text-right font-bold text-gray-500">Total Tagihan</td>
                            <td class="p-3 text-right font-bold text-npcGreen text-lg"><?php echo format_rupiah($order_data['total_amount']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="p-3 text-right font-medium text-sm text-gray-400">Status Pembayaran</td>
                            <td class="p-3 text-right">
                                <?php if($order_data['payment_status'] == 'paid' || $order_data['payment_status'] == 'verified'): ?>
                                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">LUNAS</span>
                                <?php else: ?>
                                    <span class="text-xs font-bold text-orange-600 bg-orange-100 px-2 py-1 rounded uppercase"><?php echo $order_data['payment_status']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="mt-8 text-center">
            <a href="index.php" class="text-gray-400 hover:text-npcGreen text-sm font-medium transition">
                &larr; Kembali ke Halaman Utama
            </a>
        </div>
    </section>
    <?php endif; ?>
</body>
</html>