<?php
include '../components/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Nunito', sans-serif !important; background-color: #f8f9fa; }
    .hero-header { background: linear-gradient(90deg, #0d47a1 0%, #42a5f5 100%); padding: 50px 0 80px 0; color: white; margin-bottom: 40px; }
    .overlap-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); padding: 2rem; margin-top: -60px; position: relative; z-index: 10; border: 1px solid #edf2f7; }
    
    .form-control-soft { background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; font-weight: 600; }
    .form-control-soft:focus { background: white; border-color: #0dcaf0; box-shadow: none; }
    .label-soft { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 6px; letter-spacing: 0.5px; }
    .table thead th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding: 15px; }
</style>

<div class="hero-header">
    <div class="container">
        <small class="text-uppercase" style="letter-spacing: 2px; opacity: 0.8;">DOSEN PANEL</small>
        <h1 class="fw-bold display-5 mt-1">Bank Materi</h1>
        <p class="opacity-75 mb-0">Upload dan kelola bahan ajar digital.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="overlap-card">
        <div class="row g-5">
            <div class="col-lg-4 border-end">
                <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">Upload Materi</h5>
                <form id="formUpload">
                    <div class="mb-3"><label class="label-soft">Mata Kuliah</label><select class="form-select form-control-soft" name="mata_kuliah_id"><option value="1">IF101</option><option value="2">IF102</option></select></div>
                    <div class="mb-3"><label class="label-soft">Judul</label><input type="text" class="form-control form-control-soft" name="judul" required></div>
                    <div class="mb-3"><label class="label-soft">Deskripsi</label><textarea class="form-control form-control-soft" name="deskripsi" rows="3"></textarea></div>
                    <div class="mb-4"><label class="label-soft">File (PDF/PPT)</label><input type="file" class="form-control form-control-soft" name="file" required></div>
                    <button type="submit" class="btn btn-info text-white w-100 rounded-pill fw-bold py-2">Upload Sekarang</button>
                </form>
            </div>
            <div class="col-lg-8 ps-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark">Arsip Materi</h5>
                    <input type="text" id="search" placeholder="Cari..." class="form-control form-control-sm w-25 bg-light border-0">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead><tr><th class="ps-2">Materi</th><th>Tanggal</th><th class="text-end pe-2">Aksi</th></tr></thead>
                        <tbody id="tbodyMateri"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const API = '../api/upload_materi.php';
document.getElementById('formUpload').addEventListener('submit', function(e){
    e.preventDefault(); fetch(API, {method:'POST', body:new FormData(this)}).then(r=>r.json()).then(d=>{ Swal.fire('Sukses','','success'); loadData(); this.reset(); });
});
function loadData(){
    fetch(API).then(r=>r.json()).then(res=>{
        let h=''; res.data.forEach(i=>{
            h+=`<tr><td class="ps-2"><span class="badge bg-info bg-opacity-10 text-info mb-1 fw-bold">${i.nama_mk}</span><div class="fw-bold text-dark small">${i.judul}</div></td>
            <td><small class="text-muted fw-bold">${new Date(i.uploaded_at).toLocaleDateString()}</small></td>
            <td class="text-end pe-2"><button onclick="hapus(${i.id})" class="btn btn-light text-danger btn-sm rounded-circle border"><i class="bi bi-trash"></i></button></td></tr>`;
        }); document.getElementById('tbodyMateri').innerHTML=h;
    });
}
function hapus(id){ Swal.fire({title:'Hapus?', icon:'warning', showCancelButton:true}).then(r=>{ if(r.isConfirmed){ let fd=new FormData(); fd.append('action','delete'); fd.append('id',id); fetch(API,{method:'POST', body:fd}).then(()=>{loadData()}); } }); }
document.getElementById('search').addEventListener('keyup', function(){
    let v = this.value.toLowerCase(); document.querySelectorAll('#tbodyMateri tr').forEach(r => r.style.display = r.innerText.toLowerCase().includes(v)?'':'none');
});
loadData();
</script>
<?php include '../components/footer.php'; ?>