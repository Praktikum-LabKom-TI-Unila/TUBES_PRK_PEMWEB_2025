<?php
include '../components/header.php';
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Nunito', sans-serif !important; background-color: #f8f9fa; }

    .hero-materi {
        background: linear-gradient(90deg, #0d47a1 0%, #42a5f5 100%); /* Gradasi Biru */
        padding: 50px 0 70px 0;
        color: white;
        margin-bottom: 40px;
        position: relative;
    }

    .search-pill-container {
        background: white;
        padding: 8px;
        border-radius: 50px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        max-width: 800px;
        margin-top: 25px;
    }

    .card-materi {
        border: 1px solid #edf2f7;
        border-radius: 16px;
        transition: 0.3s;
        background: white;
        height: 100%;
    }
    .card-materi:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border-color: #bbdefb;
    }
    
    .icon-file-box {
        width: 50px; height: 50px;
        background-color: #e3f2fd;
        color: #0d47a1;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    
    .badge-mk {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    
    .btn-download-outline {
        border: 1px solid #0d6efd;
        color: #0d6efd;
        border-radius: 50px;
        width: 100%;
        padding: 8px;
        font-weight: 700;
        transition: 0.2s;
    }
    .btn-download-outline:hover { background: #0d6efd; color: white; }
</style>

<div class="hero-materi">
    <div class="container">
        <small class="text-uppercase" style="letter-spacing: 2px; opacity: 0.8;">RUANG BELAJAR</small>
        <h1 class="fw-bold display-5 mt-1">Materi Perkuliahan</h1>
        <p class="opacity-75 mb-0">Akses semua sumber daya pembelajaran Anda di satu tempat.</p>
        
        <i class="bi bi-journal-bookmark-fill position-absolute end-0 top-50 translate-middle-y me-5 d-none d-lg-block" style="font-size: 8rem; opacity: 0.1;"></i>
        
        <div class="search-pill-container d-flex align-items-center">
            <i class="bi bi-search text-muted ms-3 fs-5"></i>
            <input type="text" id="search" class="form-control border-0 shadow-none py-2" placeholder="Cari judul materi atau mata kuliah...">
            <button class="btn btn-primary rounded-pill px-4 fw-bold">Cari</button>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row row-cols-1 row-cols-md-3 g-4" id="gridMateri">
        </div>
    
    <div id="empty" class="text-center py-5 d-none">
        <h5 class="fw-bold text-muted">Materi tidak ditemukan</h5>
    </div>
</div>

<script>
fetch('../api/upload_materi.php').then(r=>r.json()).then(res => {
    let h = '';
    res.data.forEach(i => {
        let date = new Date(i.uploaded_at).toLocaleDateString('id-ID', { day:'numeric', month:'long' });
        
        h += `<div class="col">
            <div class="card card-materi p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-file-box"><i class="bi bi-file-earmark-text fs-4"></i></div>
                    <span class="badge-mk">${i.nama_mk}</span>
                </div>
                
                <h5 class="fw-bold text-dark mb-2">${i.judul}</h5>
                <p class="text-muted small mb-4" style="min-height: 40px;">${i.deskripsi || 'Tidak ada deskripsi.'}</p>
                
                <div class="mt-auto border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted fw-bold"><i class="bi bi-person-circle me-1"></i> ${i.nama_dosen || 'Dosen'}</small>
                        <small class="text-muted">${date}</small>
                    </div>
                    <a href="../uploads/materi/${i.file_path}" class="btn btn-download-outline d-block text-center text-decoration-none" download>
                        Download File <i class="bi bi-download ms-1"></i>
                    </a>
                </div>
            </div>
        </div>`;
    });
    document.getElementById('gridMateri').innerHTML = h;
});

document.getElementById('search').addEventListener('keyup', function(){
    let v = this.value.toLowerCase();
    document.querySelectorAll('#gridMateri .col').forEach(c => c.style.display = c.textContent.toLowerCase().includes(v)?'':'none');
});
</script>
<?php include '../components/footer.php'; ?>