<?php
require_once __DIR__ . '/../config/auth.php';
cekRole(['pegawai']);
$page_title = 'Tentukan Sekolah';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../config/database.php';

$conn = koneksiDatabase();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);
    $sekolah_id = (int)($_POST['sekolah_id'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $porsi = (int)($_POST['porsi'] ?? 0);
    
    $menu = $conn->query("SELECT * FROM menu WHERE id = $menu_id AND status = 'disetujui'")->fetch_assoc();
    $sekolah = $conn->query("SELECT * FROM users WHERE id = $sekolah_id AND role = 'sekolah'")->fetch_assoc();
    
    if ($menu && $sekolah && $porsi > 0) {
        if ($sekolah['jumlah_siswa'] > $menu['porsi_maksimal']) {
            $error = "Jumlah siswa (" . $sekolah['jumlah_siswa'] . ") harus lebih kecil dari porsi maksimal menu (" . $menu['porsi_maksimal'] . ")";
        } elseif ($porsi > $menu['porsi_maksimal']) {
            $error = "Porsi ditentukan tidak boleh melebihi porsi maksimal menu";
        } else {
            $check = $conn->query("SELECT id FROM jadwal WHERE menu_id = $menu_id AND sekolah_id = $sekolah_id AND tanggal = '$tanggal'");
            if ($check->num_rows > 0) {
                $error = "Jadwal untuk sekolah ini pada tanggal tersebut sudah ada";
            } else {
                $pegawai_id = $_SESSION['user_id'];
                $stmt = $conn->prepare("INSERT INTO jadwal (menu_id, sekolah_id, tanggal, porsi_ditentukan, pegawai_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisii", $menu_id, $sekolah_id, $tanggal, $porsi, $pegawai_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    $conn->close();
                    header('Location: tentukan_sekolah.php?success=' . urlencode('Sekolah berhasil ditentukan'));
                    exit();
                } else {
                    $error = "Gagal menentukan sekolah: " . $stmt->error;
                    $stmt->close();
                }
            }
        }
    } else {
        $error = "Data tidak valid";
    }
}

$menus = $conn->query("SELECT * FROM menu WHERE status = 'disetujui' ORDER BY tanggal_mulai DESC")->fetch_all(MYSQLI_ASSOC);

$sekolah_list = $conn->query("SELECT * FROM users WHERE role = 'sekolah' ORDER BY nama_sekolah")->fetch_all(MYSQLI_ASSOC);

$jadwal_list = $conn->query("
    SELECT j.*, m.jenis_makanan, m.jenis_minuman, s.nama_sekolah, s.jumlah_siswa
    FROM jadwal j
    JOIN menu m ON j.menu_id = m.id
    JOIN users s ON j.sekolah_id = s.id
    ORDER BY j.tanggal DESC, j.created_at DESC
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
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Tentukan Sekolah untuk Menu</h2>
    
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
    
    <form method="POST" class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-gray-700 mb-2">Pilih Menu (Status: Disetujui) *</label>
            <select name="menu_id" id="menu_id" class="w-full border rounded px-3 py-2" required onchange="loadMenuInfo()">
                <option value="">-- Pilih Menu --</option>
                <?php foreach ($menus as $menu): ?>
                    <option value="<?php echo $menu['id']; ?>" data-porsi="<?php echo $menu['porsi_maksimal']; ?>">
                        <?php echo htmlspecialchars($menu['jenis_makanan'] . ' - ' . date('d/m/Y', strtotime($menu['tanggal_mulai']))); ?>
                        (Porsi Max: <?php echo number_format($menu['porsi_maksimal']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Pilih Sekolah *</label>
            <select name="sekolah_id" id="sekolah_id" class="w-full border rounded px-3 py-2" required onchange="loadSekolahInfo()">
                <option value="">-- Pilih Sekolah --</option>
                <?php foreach ($sekolah_list as $sekolah): ?>
                    <option value="<?php echo $sekolah['id']; ?>" data-siswa="<?php echo $sekolah['jumlah_siswa']; ?>">
                        <?php echo htmlspecialchars($sekolah['nama_sekolah'] . ' (' . number_format($sekolah['jumlah_siswa']) . ' siswa)'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Tanggal *</label>
            <input type="date" name="tanggal" class="w-full border rounded px-3 py-2" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div>
            <label class="block text-gray-700 mb-2">Porsi Ditentukan *</label>
            <input type="number" name="porsi" id="porsi" class="w-full border rounded px-3 py-2" required min="1">
            <p class="text-sm text-gray-500 mt-1">
                <span id="info-porsi"></span>
            </p>
        </div>
        
        <div class="col-span-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                <i class="fas fa-check mr-2"></i>Tentukan Sekolah
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl sm:rounded-2xl p-6 sm:p-8 card-shadow">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Daftar Jadwal</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gradient-to-r from-primary-600 to-primary-500 text-white">
                    <th class="px-4 py-3 text-left text-sm font-semibold">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Menu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Sekolah</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Jumlah Siswa</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Porsi Ditentukan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($jadwal_list)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 block text-gray-300"></i>
                        <p>Tidak ada jadwal</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($jadwal_list as $jadwal): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo date('d/m/Y', strtotime($jadwal['tanggal'])); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800">
                        <?php echo htmlspecialchars($jadwal['jenis_makanan'] . ' + ' . $jadwal['jenis_minuman']); ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo htmlspecialchars($jadwal['nama_sekolah']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($jadwal['jumlah_siswa']); ?></td>
                    <td class="px-4 py-3 text-sm text-gray-800"><?php echo number_format($jadwal['porsi_ditentukan']); ?></td>
                    <td class="px-4 py-3">
                        <a href="hapus_jadwal.php?id=<?php echo $jadwal['id']; ?>" 
                           onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                            <i class="fas fa-trash"></i>
                            <span>Hapus</span>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function loadMenuInfo() {
    const select = document.getElementById('menu_id');
    const option = select.options[select.selectedIndex];
    const porsiMax = option.getAttribute('data-porsi');
    
    if (porsiMax) {
        document.getElementById('info-porsi').textContent = 'Porsi maksimal menu: ' + parseInt(porsiMax).toLocaleString('id-ID');
        document.getElementById('porsi').max = porsiMax;
    } else {
        document.getElementById('info-porsi').textContent = '';
    }
}

function loadSekolahInfo() {
    const select = document.getElementById('sekolah_id');
    const option = select.options[select.selectedIndex];
    const jumlahSiswa = option.getAttribute('data-siswa');
    
    if (jumlahSiswa) {
        const info = document.getElementById('info-porsi');
        const currentInfo = info.textContent;
        info.textContent = currentInfo + ' | Jumlah siswa: ' + parseInt(jumlahSiswa).toLocaleString('id-ID');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
    </main>
</div>

