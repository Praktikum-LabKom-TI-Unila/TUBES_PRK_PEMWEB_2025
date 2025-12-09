<?php
include '../components/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Nunito', sans-serif !important; background-color: #f8f9fa; }

    .hero-header {
        background: linear-gradient(90deg, #0d47a1 0%, #42a5f5 100%);
        padding: 50px 0 80px 0;
        color: white;
        margin-bottom: 40px;
    }

    .overlap-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        padding: 2rem;
        margin-top: -60px;
        position: relative;
        z-index: 10;
        border: 1px solid #edf2f7;
    }

    .stat-card { border-radius: 12px; padding: 1.5rem; height: 100%; border: none; }
    .stat-blue { background: #0d6efd; color: white; }
    .stat-white { background: #f8f9fa; border: 1px solid #e9ecef; border-left: 5px solid; }
    .border-yellow { border-left-color: #ffc107; }
    .border-green { border-left-color: #198754; }

    .table thead th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding: 15px; }
    .badge-soft-warning { background: #fffbeb; color: #b45309; padding: 6px 12px; border-radius: 20px; font-weight: 700; }
    .badge-soft-success { background: #f0fdf4; color: #15803d; padding: 6px 12px; border-radius: 20px; font-weight: 700; }
    .badge-soft-danger { background: #fef2f2; color: #b91c1c; padding: 6px 12px; border-radius: 20px; font-weight: 700; }
</style>

<div class="hero-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <small class="text-uppercase" style="letter-spacing: 2px; opacity: 0.8;">AKADEMIK</small>
            <h1 class="fw-bold display-5 mt-1">Daftar Tugas</h1>
            <p class="opacity-75 mb-0">Pantau deadline dan kumpulkan tugas tepat waktu.</p>
        </div>
        <button class="btn btn-light fw-bold px-4 rounded-pill shadow-sm text-primary"><i class="bi bi-funnel me-2"></i> Filter</button>
    </div>
</div>

<div class="container pb-5">
    <div class="overlap-card">
        
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card stat-blue d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3"><i class="bi bi-layers fs-4"></i></div>
                    <div><small class="opacity-75 fw-bold d-block">Total Tugas</small><h2 class="fw-bold mb-0" id="statTotal">-</h2></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-white border-yellow d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3"><i class="bi bi-clock-history fs-4"></i></div>
                    <div><small class="text-muted fw-bold d-block">Pending</small><h2 class="fw-bold mb-0 text-dark" id="statPending">-</h2></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-white border-green d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3"><i class="bi bi-check-circle fs-4"></i></div>
                    <div><small class="text-muted fw-bold d-block">Selesai</small><h2 class="fw-bold mb-0 text-dark" id="statDone">-</h2></div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th class="ps-3">Mata Kuliah</th><th>Detail</th><th>Deadline</th><th>Status</th><th class="text-end pe-3">Aksi</th></tr></thead>
                <tbody id="tbodyTugas"><tr><td colspan="5" class="text-center py-5">Loading...</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSubmit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="mb-3 text-primary bg-primary bg-opacity-10 d-inline-block p-3 rounded-circle"><i class="bi bi-cloud-upload fs-1"></i></div>
                <h5 class="fw-bold">Upload Jawaban</h5>
                <form id="formSubmitTugas">
                    <input type="hidden" name="tugas_id" id="tugasId">
                    <input type="file" class="form-control bg-light border-0 mb-4 py-2 px-3 rounded-3" name="file" required>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">Kirim Jawaban</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function loadTugas() {
    fetch('../api/submit_tugas.php?t='+Date.now()).then(r=>r.json()).then(res => {
        let total = res.data.length, done = res.data.filter(i => i.status_kumpul === 'submitted').length;
        document.getElementById('statTotal').innerText = total; document.getElementById('statDone').innerText = done; document.getElementById('statPending').innerText = total - done;

        let h = '';
        res.data.forEach(i => {
            let status = `<span class="badge-soft-warning">Pending</span>`, btn = `<button onclick="bukaModal(${i.id})" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Upload</button>`;
            if(i.status_kumpul == 'submitted') { status = `<span class="badge-soft-success">Terkirim</span>`; btn = `<button onclick="bukaModal(${i.id})" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">Update</button>`; }
            
            h += `<tr><td class="ps-3 fw-bold text-dark">${i.nama_mk}</td><td><div class="fw-bold text-dark">${i.judul}</div><small class="text-muted">${i.deskripsi}</small></td>
            <td><span class="badge-soft-danger">${i.deadline.replace('T', ' ')}</span></td><td>${status}</td><td class="text-end pe-3">${btn}</td></tr>`;
        });
        document.querySelector('#tbodyTugas').innerHTML = h;
    });
}
function bukaModal(id){ document.getElementById('tugasId').value=id; new bootstrap.Modal(document.getElementById('modalSubmit')).show(); }
document.getElementById('formSubmitTugas').addEventListener('submit', function(e){
    e.preventDefault(); fetch('../api/submit_tugas.php', {method:'POST', body:new FormData(this)}).then(r=>r.json()).then(d=>{ Swal.fire('Sukses',d.message,'success'); loadTugas(); });
});
loadTugas();
</script>
<?php include '../components/footer.php'; ?>