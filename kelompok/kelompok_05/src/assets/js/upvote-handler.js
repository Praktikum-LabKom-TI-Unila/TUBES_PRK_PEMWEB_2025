document.addEventListener('DOMContentLoaded', function() {
    const voteButtons = document.querySelectorAll('.vote-btn');
    const isLoggedIn = document.body.hasAttribute('data-logged-in');
    let isRedirecting = false; // Flag untuk mencegah multiple clicks
    
    voteButtons.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // CEK LOGIN DULU - Jika belum login, redirect langsung
            // JANGAN UBAH APAPUN sebelum cek ini!
            if (!isLoggedIn) {
                // Jika sudah dalam proses redirect, abaikan click
                if (isRedirecting) return;
                
                isRedirecting = true;
                showToast('Anda harus login terlebih dahulu untuk vote', 'warning');
                setTimeout(() => {
                    window.location.href = '../auth/login.php';
                }, 1000);
                return; // STOP DI SINI - tidak ada perubahan DOM
            }
            
            // Hanya sampai sini jika user sudah login
            const pengaduanId = this.getAttribute('data-pengaduan-id');
            const isUpvoted = this.getAttribute('data-upvoted') === '1';
            
            // Disable button sementara (hanya untuk user yang login)
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Loading...';
            
            try {
                const formData = new FormData();
                formData.append('pengaduan_id', pengaduanId);
                
                const response = await fetch('api/upvote.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Update button state
                    const newUpvoted = result.action === 'added';
                    this.setAttribute('data-upvoted', newUpvoted ? '1' : '0');
                    
                    // Update button appearance
                    if (newUpvoted) {
                        this.classList.remove('btn-primary');
                        this.classList.add('btn-success');
                        this.innerHTML = '<i class="bi bi-hand-thumbs-up-fill"></i> Voted';
                    } else {
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                        this.innerHTML = '<i class="bi bi-hand-thumbs-up"></i> Vote';
                    }
                    
                    // Update counter
                    const counterElement = document.querySelector(`#vote-count-${pengaduanId} .vote-number`);
                    if (counterElement) {
                        counterElement.textContent = result.total_upvotes;
                        
                        // Animate counter
                        counterElement.parentElement.classList.add('pulse-vote');
                        setTimeout(() => {
                            counterElement.parentElement.classList.remove('pulse-vote');
                        }, 600);
                    }
                    
                    // Show success toast
                    showToast(result.message, 'success');
                    
                } else if (result.require_login) {
                    showToast('Sesi login Anda telah berakhir. Silakan login kembali.', 'warning');
                    setTimeout(() => {
                        window.location.href = '../auth/login.php';
                    }, 1500);
                } else {
                    showToast(result.message, 'error');
                    this.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Upvote error:', error);
                showToast('Terjadi kesalahan. Silakan coba lagi.', 'error');
                this.innerHTML = originalText;
            } finally {
                this.disabled = false;
            }
        });
    });
    
    // Reset button states saat halaman di-load dari bfcache (browser back/forward)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Halaman di-load dari bfcache, reset semua button
            const allButtons = document.querySelectorAll('.vote-btn');
            allButtons.forEach(btn => {
                btn.disabled = false;
                
                // Restore button appearance berdasarkan data-upvoted
                const isUpvoted = btn.getAttribute('data-upvoted') === '1';
                if (isUpvoted) {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bi bi-hand-thumbs-up-fill"></i> Voted';
                } else {
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.innerHTML = '<i class="bi bi-hand-thumbs-up"></i> Vote';
                }
            });
        }
    });
});

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'exclamation-triangle'}"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
