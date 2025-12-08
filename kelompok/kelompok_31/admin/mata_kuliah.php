<?php
/**
 * CRUD Mata Kuliah
 * Dikerjakan oleh: Anggota 2
 * 
 * TODO:
 * - Tampilkan daftar mata kuliah
 * - CRUD operations dengan AJAX
 * - Assign dosen
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Manajemen Mata Kuliah";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Manajemen Mata Kuliah</h2>
    
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahModal">
        Tambah Mata Kuliah
    </button>
    
    <div id="daftarMataKuliah">
        <!-- TODO: Load data via AJAX -->
        <p>Loading...</p>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="tambahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formMataKuliah">
                    <!-- TODO: Form fields -->
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>
