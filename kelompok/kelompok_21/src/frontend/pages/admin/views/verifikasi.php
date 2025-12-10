<h2 class="mb-4">Verifikasi Akun Tutor</h2>

<ul class="nav nav-tabs mb-4" id="verifTabs" role="tablist">
  <li class="nav-item">
    <button class="nav-link active fw-bold" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-content" type="button">
        Menunggu Review <span class="badge bg-danger ms-1" id="count-pending">2</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link text-muted" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content" type="button">
        Riwayat Keputusan
    </button>
  </li>
</ul>

<div class="tab-content">
    
    <div class="tab-pane fade show active" id="pending-content">
        <div class="row" id="pending-list">
            
            <div class="col-md-6 mb-4" id="card-rizky">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-warning text-dark me-2">Pending</span>
                            <span class="badge bg-light text-dark border">ID: REG-2025-001</span>
                        </div>
                        <small class="text-muted"><i class="far fa-clock me-1"></i> 2 jam lalu</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <img src="https://ui-avatars.com/api/?name=Rizky+Febian&background=random" class="rounded-circle me-3" width="60">
                            <div>
                                <h5 class="mb-1 fw-bold">Rizky Febian</h5>
                                <p class="text-muted mb-0 small">Mendaftar sebagai: <strong>Tutor Musik</strong></p>
                                <small class="text-secondary"><i class="fas fa-university me-1"></i> Univ. Pendidikan Indonesia</small>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Dokumen Persyaratan</h6>
                        <ul class="list-group list-group-flush mb-4 small">
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                                <div><i class="fas fa-file-pdf text-danger me-2"></i> Curriculum Vitae (CV)</div>
                                <button class="btn btn-sm btn-outline-primary bg-white" onclick="viewDoc('CV - Rizky')">Lihat</button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                                <div><i class="fas fa-image text-success me-2"></i> Scan KTM / KTP</div>
                                <button class="btn btn-sm btn-outline-primary bg-white" onclick="viewDoc('KTM - Rizky')">Lihat</button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded border-0">
                                <div><i class="fas fa-file-alt text-primary me-2"></i> Transkrip Nilai</div>
                                <button class="btn btn-sm btn-outline-primary bg-white" onclick="viewDoc('Transkrip - Rizky')">Lihat</button>
                            </li>
                        </ul>

                        <div class="d-grid gap-2 d-md-flex">
                            <button class="btn btn-success flex-grow-1 py-2" onclick="processVerif('card-rizky', 'Rizky Febian', 'Tutor Musik', 'Diterima')">
                                <i class="fas fa-check-circle me-2"></i> Terima Pengajuan
                            </button>
                            <button class="btn btn-outline-danger flex-grow-1 py-2" onclick="processVerif('card-rizky', 'Rizky Febian', 'Tutor Musik', 'Ditolak')">
                                <i class="fas fa-times-circle me-2"></i> Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4" id="card-sarah">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-warning text-dark me-2">Pending</span>
                            <span class="badge bg-light text-dark border">ID: REG-2025-002</span>
                        </div>
                        <small class="text-muted"><i class="far fa-clock me-1"></i> 1 hari lalu</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4 border-bottom pb-3">
                            <img src="https://ui-avatars.com/api/?name=Sarah+V&background=random" class="rounded-circle me-3" width="60">
                            <div>
                                <h5 class="mb-1 fw-bold">Sarah Viloid</h5>
                                <p class="text-muted mb-0 small">Mendaftar sebagai: <strong>Tutor E-Sport</strong></p>
                                <small class="text-secondary"><i class="fas fa-university me-1"></i> Binus University</small>
                            </div>
                        </div>
                        
                        <h6 class="fw-bold text-uppercase small text-muted mb-3">Dokumen Persyaratan</h6>
                        <ul class="list-group list-group-flush mb-4 small">
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded mb-2 border-0">
                                <div><i class="fas fa-file-pdf text-danger me-2"></i> Portfolio_Game.pdf</div>
                                <button class="btn btn-sm btn-outline-primary bg-white" onclick="viewDoc('Portfolio - Sarah')">Lihat</button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center bg-light rounded border-0">
                                <div><i class="fas fa-image text-success me-2"></i> Sertifikat Kompetensi</div>
                                <button class="btn btn-sm btn-outline-primary bg-white" onclick="viewDoc('Sertifikat - Sarah')">Lihat</button>
                            </li>
                        </ul>
                        
                        <div class="alert alert-danger py-2 px-3 small mb-4">
                            <i class="fas fa-exclamation-triangle me-1"></i> <strong>Catatan Sistem:</strong> Scan KTP terlihat buram.
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button class="btn btn-success flex-grow-1 py-2" onclick="processVerif('card-sarah', 'Sarah Viloid', 'Tutor E-Sport', 'Diterima')">
                                <i class="fas fa-check-circle me-2"></i> Terima
                            </button>
                            <button class="btn btn-outline-danger flex-grow-1 py-2" onclick="processVerif('card-sarah', 'Sarah Viloid', 'Tutor E-Sport', 'Ditolak')">
                                <i class="fas fa-times-circle me-2"></i> Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div id="empty-msg" class="alert alert-success text-center d-none p-5">
            <div class="mb-3"><i class="fas fa-check-double fa-3x text-success opacity-50"></i></div>
            <h5 class="fw-bold">Semua Beres!</h5>
            <p class="text-muted">Tidak ada permintaan verifikasi baru saat ini.</p>
        </div>
    </div>

    <div class="tab-pane fade" id="history-content">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="history-list">
                    <div class="text-center text-muted py-5" id="history-empty">
                        <i class="fas fa-history fa-2x mb-3 opacity-25"></i>
                        <p>Belum ada riwayat verifikasi hari ini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function viewDoc(docName) {
        Swal.fire({
            title: docName,
            text: 'Ini adalah simulasi tampilan dokumen PDF/Gambar.',
            icon: 'info',
            confirmButtonText: 'Tutup Preview'
        });
    }

    function processVerif(cardId, nama, role, keputusan) {
        let confirmColor = keputusan === 'Diterima' ? '#198754' : '#dc3545';
        let confirmText = keputusan === 'Diterima' ? 'Ya, Aktifkan Akun' : 'Ya, Tolak Pengajuan';

        Swal.fire({
            title: 'Konfirmasi Keputusan',
            html: `Anda akan menandai <b>${nama}</b> sebagai <b style="color:${confirmColor}">${keputusan}</b>.<br>Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const cardElement = document.getElementById(cardId);
                cardElement.style.transition = "all 0.5s ease";
                cardElement.style.transform = "translateX(100px)";
                cardElement.style.opacity = "0";
                
                setTimeout(() => {
                    cardElement.classList.add('d-none'); 
                    updateBadgeCount(); 
                }, 500);

                addToHistory(nama, role, keputusan);

                showToast(`Status ${nama} diperbarui: ${keputusan}`, keputusan === 'Diterima' ? 'success' : 'error');
            }
        });
    }

    function updateBadgeCount() {
        let badge = document.getElementById('count-pending');
        let count = parseInt(badge.innerText) - 1;
        badge.innerText = count;

        if(count <= 0) {
            badge.style.display = 'none'; 
            document.getElementById('empty-msg').classList.remove('d-none');
        }
    }

    function addToHistory(nama, role, keputusan) {
        const historyContainer = document.getElementById('history-list');
        const emptyMsg = document.getElementById('history-empty');
        if(emptyMsg) emptyMsg.style.display = 'none';

        let badgeClass = keputusan === 'Diterima' ? 'bg-success' : 'bg-danger';
        let iconClass = keputusan === 'Diterima' ? 'check' : 'times';

        const historyItem = `
            <div class="list-group-item d-flex justify-content-between align-items-center p-3 border-bottom animate__animated animate__fadeInDown">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name=${nama}&background=random" class="rounded-circle me-3" width="40">
                    <div>
                        <h6 class="mb-0 fw-bold">${nama}</h6>
                        <small class="text-muted">${role}</small>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge ${badgeClass} mb-1">
                        <i class="fas fa-${iconClass} me-1"></i> ${keputusan}
                    </span>
                    <div class="small text-muted" style="font-size: 0.75rem;">Baru saja</div>
                </div>
            </div>
        `;
        
        historyContainer.insertAdjacentHTML('afterbegin', historyItem);
    }
</script>