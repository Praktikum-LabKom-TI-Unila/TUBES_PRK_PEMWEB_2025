<?php
include '../components/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Nunito', sans-serif !important; background-color: #f8f9fa; }
    
    .hero-header { background: linear-gradient(90deg, #0d47a1 0%, #42a5f5 100%); padding: 50px 0 80px 0; color: white; margin-bottom: 40px; }
    .overlap-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 2rem; margin-top: -60px; position: relative; z-index: 10; border: 1px solid #edf2f7; }
    
    .form-control-soft { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; font-weight: 600; }
    .form-control-soft:focus { background: white; border-color: #0d6efd; box-shadow: none; }
    .label-soft { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 6px; letter-spacing: 0.5px; }
    
    .table thead th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding: 15px; }
</style>

<div class="hero-header">
    <div class="container">
        <small class="text-uppercase" style="letter-spacing: 2px; opacity: 0.8;">DOSEN PANEL</small>
        <h1 class="fw-bold display-5 mt-1">Kelola Tugas</h1>
        <p class="opacity-75 mb-0">Buat dan distribusikan tugas kepada mahasiswa.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="overlap-card">
        <div class="row g-5">
            <div class="col-lg-5 border-end">
                <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">Formulir Tugas Baru</h5>
                <form id="formTugas">
                    <div class="mb-3"><label class="label-soft">Judul Tugas</label><input type="text" class="form-control form-control-soft" name="judul" required></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label class="label-soft">Mata Kuliah</label><select class="form-select form-control-soft" name="mata_kuliah_id" required><option value="1">IF101</option><option value="2">IF102</option></select></div>
                        <div class="col-6"><label class="label-soft">Deadline</label><input type="datetime-local" class="form-control form-control-soft" name="deadline" required></div>
                    </div>
                    <div class="mb-3"><label class="label-soft">Instruksi</label><textarea class="form-control form-control-soft" name="deskripsi" rows="3"></textarea></div>
                    <div class="mb-4"><label class="label-soft">File Soal</label><input type="file" class="form-control form-control-soft" name="file"></div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">Terbitkan Tugas</button>
                </form>
            </div>
            <div class="col-lg-7 ps-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Riwayat Tugas</h5>
                    <button onclick="loadData()" class="btn btn-light btn-sm border rounded-circle"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th class="ps-2">Detail</th><th>Deadline</th><th class="text-end pe-2">Hapus</th></tr></thead>
                        <tbody id="tbodyHistory"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const API = '../api/buat_tugas.php';
document.getElementById('formTugas').addEventListener('submit', function(e){
    e.preventDefault(); fetch(API, {method:'POST', body:new FormData(this)}).then(r=>r.json()).then(d=>{ Swal.fire('Sukses','','success'); loadData(); this.reset(); });
});
function loadData(){
    fetch(API).then(r=>r.json()).then(res=>{
        let h=''; res.data.forEach(i=>{
            h+=`<tr><td class="ps-2"><span class="badge bg-primary bg-opacity-10 text-primary mb-1 fw-bold">${i.nama_mk}</span><div class="fw-bold text-dark small">${i.judul}</div></td>
            <td><span class="text-danger fw-bold small">${new Date(i.deadline).toLocaleString()}</span></td>
            <td class="text-end pe-2"><button onclick="hapus(${i.id})" class="btn btn-light text-danger btn-sm rounded-circle border"><i class="bi bi-trash"></i></button></td></tr>`;
        }); document.getElementById('tbodyHistory').innerHTML=h;
    });
}
function hapus(id){ Swal.fire({title:'Hapus?', icon:'warning', showCancelButton:true}).then(r=>{ if(r.isConfirmed){ let fd=new FormData(); fd.append('action','delete'); fd.append('id',id); fetch(API,{method:'POST', body:fd}).then(()=>{loadData()}); } }); }
loadData();
</script>
<?php include '../components/footer.php'; ?>