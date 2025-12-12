<?php
include('../config/koneksi.php');
include('../layout/header.php');

$query = "
    SELECT 
        b.*, 
        s.nama_supplier 
    FROM barang b
    LEFT JOIN suppliers s ON b.id_supplier = s.id_supplier
    WHERE b.is_active = '1'
    ORDER BY b.id_barang DESC
";
$result = mysqli_query($conn, $query); 

$barangs = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $barangs[] = $row;
    }
}

$query_suppliers = "SELECT id_supplier, nama_supplier FROM suppliers WHERE is_active = '1' ORDER BY nama_supplier ASC";
$result_suppliers = mysqli_query($conn, $query_suppliers);
$suppliers_list = [];
if ($result_suppliers) {
    while ($row = mysqli_fetch_assoc($result_suppliers)) {
        $suppliers_list[] = $row;
    }
}

$status = $_GET['status'] ?? '';
$pesan = $_GET['pesan'] ?? '';
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; }
.wrapper-flex { display: flex; min-height: 100vh; width: 100%; }
.content-wrapper { background: #f4f6f9; flex: 1; padding: 30px; min-height: 100vh; }
.card { border: none; border-radius: 15px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); background: white; overflow: hidden; margin-bottom: 20px; }
.card-header { border: none; padding: 20px 25px; }
.card-title { font-weight: 700; color: #1B3C53; font-size: 1.1rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
.table thead th { background: linear-gradient(90deg, #1B3C53, #2F5C83); color: white; border: none; font-weight: 500; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding: 15px; }
.table tbody td { vertical-align: middle; padding: 15px; color: #444; border-bottom: 1px solid #f2f2f2; font-size: 0.95rem; font-weight: 500; }
.table tbody tr:hover { background-color: #f1f7fd; transition: all 0.2s ease; }
.btn { border-radius: 50px; padding: 8px 20px; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
.btn:hover { transform: translateY(-2px); }
.btn-primary { background: linear-gradient(45deg, #1B3C53, #4a90e2); border: none; }
.btn-warning { background: linear-gradient(45deg, #f1c40f, #f39c12); border: none; color: white !important; }
.btn-danger { background: linear-gradient(45deg, #e74c3c, #c0392b); border: none; }
.badge { padding: 8px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; }
.badge-info { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdefb; }
.badge-warning { background: #fff8e1; color: #f57f17; border: 1px solid #ffecb3; }
.badge-secondary { background: #e9ecef; color: #343a40; border: 1px solid #dee2e6; }
.modal-content { border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
.modal-header { background: linear-gradient(45deg, #1B3C53, #2F5C83); border-radius: 20px 20px 0 0; padding: 25px 30px; }
.modal-title { font-weight: 800; color: white !important; font-size: 1.25rem; }
.close { color: black !important; opacity: 1; font-size: 1.5rem; text-shadow: none; }
.form-label { font-weight: 700; color: #2c3e50; margin-bottom: 8px; display: block; font-size: 0.95rem; text-transform: none; }
.form-control { border-radius: 10px !important; padding: 14px 15px !important; background-color: #f8f9fa; border: 1px solid #e0e0e0 !important; font-size: 1rem; color: #333; font-weight: 500; height: auto; }
.form-control:focus { border-color: #1B3C53; box-shadow: 0 0 0 3px rgba(27,60,83,0.1); background-color: #fff; }
.alert {position: relative; padding-right: 45px !important;}
.alert .close {position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none;border: none;font-size: 20px;opacity: 0.7;}
</style>

<div class="wrapper-flex">
    <?php include('../layout/sidebar.php'); ?>
    <div class="content-wrapper">
        <section class="content-header mb-4">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-sm-12">
                        <h1 style="color: #1B3C53; font-weight: 800; font-size: 2rem; letter-spacing: -1px;">
                            <i class="fas fa-box-open mr-2"></i> Master Data Barang Titipan
                        </h1>
                        <p class="text-muted m-0" style="font-size: 1rem;">Kelola inventori, harga, dan supplier untuk setiap barang</p>
                    </div>
                </div>
            </div>
        </section>

        <?php if ($status && $pesan): ?>
            <div class="alert <?= $status==='sukses'?'alert-success':'alert-danger'; ?> alert-dismissible fade show" role="alert" style="border-radius:10px;margin:0 25px 20px 25px;">
                <strong><?= $status==='sukses'?'Sukses: ':'Gagal: '; ?></strong><?= htmlspecialchars($pesan); ?>
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <section class="content">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-boxes me-2" style="color: #1B3C53;"></i>
                        <h3 class="card-title">Daftar Barang Aktif</h3>
                    </div>
                    <?php if ($_SESSION['role']==='admin'): ?>
                        <button type="button" class="btn btn-primary" onclick="resetModal(); showModal();">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Barang
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th>Nama Barang</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Supplier/Vendor</th>
                                <?php if ($_SESSION['role']==='admin'): ?>
                                    <th width="15%" class="text-center">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($barangs as $barang): ?>
                            <tr>
                                <td class="text-center font-weight-bold" style="color: #1B3C53;"><?= $no++; ?></td>
                                <td style="font-weight:600; color:#2c3e50;"><?= htmlspecialchars($barang['nama_barang']); ?></td>
                                <td>Rp <?= number_format($barang['harga_jual'],0,',','.'); ?></td>
                                <td><?= htmlspecialchars($barang['stok']); ?></td>
                                <td>
                                    <?php 
                                        if($barang['id_supplier']===NULL){
                                            echo '<span class="badge badge-secondary"><i class="fas fa-building mr-1"></i> Koperasi (Internal)</span>';
                                        }else{
                                            echo '<span class="badge badge-warning"><i class="fas fa-truck-moving mr-1"></i> '.htmlspecialchars($barang['nama_supplier']).'</span>';
                                        }
                                    ?>
                                </td>
                                <?php if($_SESSION['role']==='admin'): ?>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" type="button"
                                        data-id="<?= $barang['id_barang']; ?>"
                                        data-nama="<?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES); ?>"
                                        data-jual="<?= $barang['harga_jual']; ?>"
                                        data-stok="<?= htmlspecialchars($barang['stok']); ?>"
                                        data-supplier="<?= $barang['id_supplier']; ?>"
                                        title="Edit Data"
                                        onclick="handleEditClick(this)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="../proses/barang_proses.php?action=arsip&id=<?= $barang['id_barang']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Anda yakin ingin mengarsipkan Barang ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal Tambah/Ubah -->
<div class="modal fade" id="modalTambahUbah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahUbahLabel"><i class="fas fa-box mr-2"></i> Tambah Barang Baru</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formBarang" action="../proses/barang_proses.php" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_barang" id="id_barang">
                    <input type="hidden" name="action" id="action" value="tambah">
                    <input type="hidden" name="ajax" value="1">

                    <div class="form-group mb-4">
                        <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required autocomplete="off">
                    </div>
                    <div class="form-group mb-4">
                        <label for="harga_jual" class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="harga_jual" name="harga_jual" required min="0">
                    </div>
                    <div class="form-group mb-4">
                        <label for="stok" class="form-label">Stok Awal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stok" name="stok" required min="1">
                    </div>
                    <div class="form-group mb-3">
                        <label for="id_supplier" class="form-label">Supplier / Penitip <span class="text-danger">*</span></label>
                        <select class="form-control" id="id_supplier" name="id_supplier" required>
                            <option value="0" selected>-- Barang Koperasi (Internal) --</option>
                            <?php foreach($suppliers_list as $s): ?>
                                <option value="<?= $s['id_supplier']; ?>"><?= htmlspecialchars($s['nama_supplier']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitModal">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('../layout/footer.php'); ?>

<script>
function resetModal(){
    document.getElementById('modalTambahUbahLabel').innerHTML='<i class="fas fa-box mr-2"></i> Tambah Barang Baru';
    document.getElementById('action').value='tambah';
    document.getElementById('id_barang').value='';
    document.getElementById('nama_barang').value='';
    document.getElementById('harga_jual').value='';
    document.getElementById('stok').value='';
    document.getElementById('id_supplier').value='0';
    document.getElementById('btnSubmitModal').innerHTML='Simpan Data';
}

function showModal(){
    var modalEl=document.getElementById('modalTambahUbah');
    var modal=new bootstrap.Modal(modalEl);
    modal.show();
}

function handleEditClick(el){
    document.getElementById('modalTambahUbahLabel').innerHTML='<i class="fas fa-edit mr-2"></i> Edit Data Barang';
    document.getElementById('action').value='ubah';
    document.getElementById('id_barang').value=el.dataset.id;
    document.getElementById('nama_barang').value=el.dataset.nama;
    document.getElementById('harga_jual').value=el.dataset.jual;
    document.getElementById('stok').value=el.dataset.stok;
    document.getElementById('id_supplier').value=el.dataset.supplier||'0';
    document.getElementById('btnSubmitModal').innerHTML='<i class="fas fa-check-circle mr-2"></i> Perbarui Data';
    showModal();
}

document.addEventListener('DOMContentLoaded', function(){
    if($("#example1").length){
        $("#example1").DataTable({
            responsive:true,
            lengthChange:false,
            autoWidth:false,
            buttons:["copy","csv","excel","pdf","print","colvis"],
            language:{"url":"//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"}
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    }
});
</script>
