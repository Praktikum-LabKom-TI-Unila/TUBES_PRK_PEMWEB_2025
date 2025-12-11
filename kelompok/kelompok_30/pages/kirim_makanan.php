<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['vendor']);
$page_title = 'Kirim Makanan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();
$vendor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['bukti_pengiriman'])) {
    $jadwal_id = (int)($_POST['jadwal_id'] ?? 0);
    $porsi_dikirim = (int)($_POST['porsi_dikirim'] ?? 0);
    $catatan = $_POST['catatan'] ?? '';
    
    $upload_dir = __DIR__ . '/../uploads/bukti_pengiriman/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $uploaded_files = [];
    
    if (isset($_FILES['bukti_pengiriman'])) {
        $files = $_FILES['bukti_pengiriman'];
        
        if (is_array($files['name'])) {
            $file_count = count($files['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] == 0) {
                    $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                    $file_name = 'bukti_' . time() . '_' . $vendor_id . '_' . $i . '.' . $ext;
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $file_name)) {
                        $uploaded_files[] = $file_name;
                    }
                }
            }
        } else {
            if ($files['error'] == 0) {
                $ext = pathinfo($files['name'], PATHINFO_EXTENSION);
                $file_name = 'bukti_' . time() . '_' . $vendor_id . '.' . $ext;
                if (move_uploaded_file($files['tmp_name'], $upload_dir . $file_name)) {
                    $uploaded_files[] = $file_name;
                }
            }
        }
    }
    
    $bukti_pengiriman_json = !empty($uploaded_files) ? json_encode($uploaded_files) : null;
    
    $jadwal = $conn->query("SELECT * FROM jadwal WHERE id = $jadwal_id")->fetch_assoc();
    if ($jadwal && $porsi_dikirim > 0) {
        $tanggal_pengiriman = date('Y-m-d');
        $sekolah_id = $jadwal['sekolah_id'];
        
        $stmt = $conn->prepare("INSERT INTO pengiriman (jadwal_id, vendor_id, sekolah_id, tanggal_pengiriman, porsi_dikirim, bukti_pengiriman, catatan, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'dikirim')");
        $stmt->bind_param("iiissss", $jadwal_id, $vendor_id, $sekolah_id, $tanggal_pengiriman, $porsi_dikirim, $bukti_pengiriman_json, $catatan);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: kirim_makanan.php?success=' . urlencode('Makanan berhasil dikirim'));
            exit();
        } else {
            $error = "Gagal mengirim makanan: " . $stmt->error;
            $stmt->close();
        }
    } else {
        $error = "Data tidak valid";
    }
}

$jadwal_list = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, s.nama_sekolah, s.jumlah_siswa,
           CASE WHEN p.id IS NULL THEN 0 ELSE 1 END as sudah_dikirim
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON j.sekolah_id = s.id
    LEFT JOIN pengiriman p ON j.id = p.jadwal_id AND p.tanggal_pengiriman = CURDATE()
    WHERE m.vendor_id = $vendor_id AND m.status = 'disetujui'
    ORDER BY j.tanggal ASC
")->fetch_all(MYSQLI_ASSOC);

$pengiriman_history = $conn->query("
    SELECT p.*, m.jenis_makanan, s.nama_sekolah
    FROM pengiriman p
    JOIN jadwal j ON p.jadwal_id = j.id
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON p.sekolah_id = s.id
    WHERE p.vendor_id = $vendor_id
    ORDER BY p.tanggal_pengiriman DESC
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

$conn->close();

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}
?>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow mb-6">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Kirim Makanan ke Sekolah</h2>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <div class="space-y-4">
        <?php foreach ($jadwal_list as $jadwal): ?>
            <?php if ($jadwal['sudah_dikirim'] == 0 && $jadwal['tanggal'] <= date('Y-m-d')): ?>
            <div class="border rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Tanggal</label>
                        <p class="text-gray-800"><?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Sekolah</label>
                        <p class="text-gray-800"><?php echo htmlspecialchars($jadwal['nama_sekolah']); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Menu</label>
                        <p class="text-gray-800"><?php echo htmlspecialchars($jadwal['jenis_makanan'] . ' + ' . $jadwal['jenis_minuman']); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold mb-1">Porsi Ditentukan</label>
                        <p class="text-gray-800"><?php echo number_format($jadwal['porsi_ditentukan']); ?></p>
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="border-t pt-4">
                    <input type="hidden" name="jadwal_id" value="<?php echo $jadwal['id']; ?>">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Porsi Dikirim *</label>
                            <input type="number" name="porsi_dikirim" class="w-full border rounded px-3 py-2" required min="1" max="<?php echo $jadwal['porsi_ditentukan']; ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 mb-2">Bukti Pengiriman (Foto) * <span class="text-sm text-gray-500">(Bisa pilih beberapa foto)</span></label>
                            <input type="file" name="bukti_pengiriman[]" accept="image/*" multiple class="w-full border rounded px-3 py-2" required>
                            <p class="text-sm text-gray-500 mt-1">Tekan Ctrl/Cmd untuk memilih beberapa file</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Catatan</label>
                        <textarea name="catatan" rows="2" class="w-full border rounded px-3 py-2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                    
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-truck mr-2"></i>Kirim
                    </button>
                </form>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty(array_filter($jadwal_list, fn($j) => $j['sudah_dikirim'] == 0 && $j['tanggal'] <= date('Y-m-d')))): ?>
        <p class="text-gray-600 text-center py-8">Tidak ada jadwal pengiriman hari ini</p>
    <?php endif; ?>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Riwayat Pengiriman</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Bukti</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($pengiriman_history)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Belum ada riwayat pengiriman</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($pengiriman_history as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y', strtotime($p['tanggal_pengiriman'])); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['nama_sekolah']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($p['jenis_makanan']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($p['porsi_dikirim']); ?></td>
                    <td class="px-4 py-3 text-sm">
                        <?php 
                        $bukti_files = [];
                        if ($p['bukti_pengiriman']) {
                            $decoded = json_decode($p['bukti_pengiriman'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $bukti_files = $decoded;
                            } else {
                                $bukti_files = [$p['bukti_pengiriman']];
                            }
                        }
                        ?>
                        <?php if (!empty($bukti_files)): ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($bukti_files as $idx => $file): ?>
                                    <a href="../uploads/bukti_pengiriman/<?php echo htmlspecialchars($file); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 hover:bg-primary-200 rounded-lg text-xs transition-colors">
                                        <i class="fas fa-image"></i>
                                        <span>Foto <?php echo $idx + 1; ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $p['status'] == 'diterima' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo ucfirst($p['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

