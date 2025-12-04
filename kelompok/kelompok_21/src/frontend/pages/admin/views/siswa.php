<?php
global $conn; 

if (!$conn) {
    echo "<div class='alert alert-danger'>Koneksi database gagal!</div>";
    exit;
}

$query = "SELECT * FROM siswa ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Siswa (Murid)</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">Export Data</button>
        </div>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSiswa">
            <i class="fas fa-plus me-1"></i> Tambah Siswa
        </button>
    </div>
</div>

<div class="row mb-3 no-print">
    <div class="col-md-3">
        <select id="filterJenjang" class="form-select bg-light border-0" onchange="filterSiswa()">
            <option value="">Semua Jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
        </select>
    </div>
    <div class="col-md-6">
        <input type="text" id="searchSiswa" class="form-control bg-light border-0" placeholder="Cari nama siswa..." onkeyup="filterSiswa()">
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="tableSiswa">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Nama Siswa</th>
                    <th>Jenjang</th>
                    <th>Sekolah</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $badgeColor = 'primary'; 
                        if($row['jenjang'] == 'SD') $badgeColor = 'info text-dark';
                        if($row['jenjang'] == 'SMP') $badgeColor = 'warning text-dark';

                        $statusColor = 'success';
                        if($row['status'] == 'Cuti') $statusColor = 'warning text-dark';
                        if($row['status'] == 'Non-Aktif') $statusColor = 'secondary';
                ?>
                
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['nama_lengkap']) ?>&background=random" class="rounded-circle me-3" width="35">
                            <div>
                                <div class="fw-bold nama-col"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-<?= $badgeColor ?> jenjang-col"><?= $row['jenjang'] ?></span></td>
                    <td><?= htmlspecialchars($row['sekolah']) ?></td>
                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                    <td><span class="badge bg-<?= $statusColor ?>"><?= $row['status'] ?></span></td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light text-info" 
                                onclick="showDetailSiswa(
                                    '<?= addslashes($row['nama_lengkap']) ?>', 
                                    '<?= $row['jenjang'] ?>', 
                                    '<?= addslashes($row['sekolah']) ?>', 
                                    '<?= addslashes($row['kelas']) ?>', 
                                    '<?= addslashes($row['minat']) ?>'
                                )">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <button class="btn btn-sm btn-light text-danger" onclick="confirmAction('Hapus siswa ini?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>Belum ada data siswa di database.</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalDetailSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Info Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="d_img" src="" class="rounded-circle shadow-sm" width="80">
                    <h4 id="d_nama" class="mt-2 fw-bold"></h4>
                    <span id="d_jenjang" class="badge bg-primary"></span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between"><span>Sekolah</span><strong id="d_sekolah"></strong></li>
                    <li class="list-group-item d-flex justify-content-between"><span>Kelas</span><strong id="d_kelas"></strong></li>
                    <li class="list-group-item">
                        <small class="text-muted d-block">Minat Belajar:</small>
                        <p id="d_minat" class="mb-0 fw-bold text-dark"></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function showDetailSiswa(nama, jenjang, sekolah, kelas, minat) {
        document.getElementById('d_nama').innerText = nama;
        document.getElementById('d_jenjang').innerText = jenjang;
        document.getElementById('d_sekolah').innerText = sekolah;
        document.getElementById('d_kelas').innerText = kelas;
        document.getElementById('d_minat').innerText = minat;
        document.getElementById('d_img').src = "https://ui-avatars.com/api/?name=" + encodeURIComponent(nama) + "&background=random";
        new bootstrap.Modal(document.getElementById('modalDetailSiswa')).show();
    }

    function filterSiswa() {
        let keyword = document.getElementById('searchSiswa').value.toLowerCase();
        let jenjang = document.getElementById('filterJenjang').value;
        let rows = document.querySelectorAll('#tableSiswa tbody tr');

        rows.forEach(row => {
            let namaEl = row.querySelector('.nama-col');
            let jgEl = row.querySelector('.jenjang-col');

            if (namaEl && jgEl) {
                let nama = namaEl.textContent.toLowerCase();
                let jg = jgEl.textContent;
                
                let matchName = nama.includes(keyword);
                let matchJenjang = jenjang === "" || jg === jenjang;
                
                row.style.display = (matchName && matchJenjang) ? "" : "none";
            }
        });
    }
</script>