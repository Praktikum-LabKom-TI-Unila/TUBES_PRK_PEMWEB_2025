<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'learner') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

$base_url_assets = "../../assets";
require_once '../../layouts/header.php';
?>

<style>
.riwayat-container {
    padding: 30px 0;
}

.riwayat-header {
    margin-bottom: 30px;
}

.riwayat-header h1 {
    color: var(--color-text-dark);
    margin-bottom: 10px;
}

.filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 10px 20px;
    background: white;
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: all 0.3s;
    font-weight: 600;
    color: var(--color-text-dark);
}

.filter-tab:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
}

.filter-tab.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.bookings-list {
    display: grid;
    gap: 20px;
}

.booking-card {
    background: white;
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border-left: 4px solid var(--color-primary);
}

.booking-card-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.booking-tutor-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.tutor-avatar-small {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.booking-details {
    margin-bottom: 15px;
}

.booking-details p {
    margin: 5px 0;
    color: var(--color-text-light);
    font-size: 14px;
}

.booking-details strong {
    color: var(--color-text-dark);
}

.booking-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-review {
    padding: 8px 16px;
    background: var(--color-secondary);
    color: var(--color-text-dark);
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-review:hover {
    background: #ffb300;
    transform: translateY(-2px);
}

.loading {
    text-align: center;
    padding: 40px;
    color: var(--color-text-light);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--color-text-light);
}

.empty-state h3 {
    margin-bottom: 10px;
    color: var(--color-text-dark);
}

/* Review Modal */
.review-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.review-modal.active {
    display: flex;
}

.review-modal-content {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.review-modal-header {
    margin-bottom: 20px;
}

.review-modal-header h2 {
    margin-bottom: 5px;
}

.rating-input {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    justify-content: center;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    font-size: 30px;
    cursor: pointer;
    color: #ddd;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input input[type="radio"]:checked ~ label,
.rating-input label:hover ~ label {
    color: #ffc107;
}

.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.close-modal {
    float: right;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--color-text-light);
}
</style>

<div class="riwayat-container">
    <div class="container">
        <div class="riwayat-header">
            <h1>Riwayat Belajar</h1>
            <p>Lihat semua sesi belajar yang telah Anda pesan</p>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'review_success'): ?>
            <div class="alert alert-success">
                Review berhasil dikirim! Terima kasih atas feedback Anda.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php
                $error = $_GET['error'];
                if ($error == 'invalid_rating') echo "Rating harus antara 1-5.";
                elseif ($error == 'empty_review') echo "Harap isi review terlebih dahulu.";
                elseif ($error == 'invalid_booking') echo "Booking tidak valid atau tidak ditemukan.";
                elseif ($error == 'review_exists') echo "Anda sudah memberikan review untuk booking ini.";
                elseif ($error == 'db_error') echo "Terjadi kesalahan database.";
                else echo "Terjadi kesalahan. Silakan coba lagi.";
                ?>
            </div>
        <?php endif; ?>

        <div class="filter-tabs">
            <div class="filter-tab active" data-filter="all">Semua</div>
            <div class="filter-tab" data-filter="pending">Pending</div>
            <div class="filter-tab" data-filter="confirmed">Confirmed</div>
            <div class="filter-tab" data-filter="completed">Completed</div>
        </div>

        <div id="bookingsList" class="bookings-list">
            <div class="loading">Memuat data...</div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="review-modal" id="reviewModal">
    <div class="review-modal-content">
        <button class="close-modal" onclick="closeReviewModal()">&times;</button>
        <div class="review-modal-header">
            <h2>Beri Rating & Review</h2>
            <p>Bagikan pengalaman belajar Anda</p>
        </div>
        <form id="reviewForm" action="../../../backend/learner/submit_review.php" method="POST">
            <input type="hidden" name="booking_id" id="reviewBookingId">
            
            <div class="rating-input">
                <input type="radio" name="rating" value="5" id="rating5" required>
                <label for="rating5">⭐</label>
                <input type="radio" name="rating" value="4" id="rating4">
                <label for="rating4">⭐</label>
                <input type="radio" name="rating" value="3" id="rating3">
                <label for="rating3">⭐</label>
                <input type="radio" name="rating" value="2" id="rating2">
                <label for="rating2">⭐</label>
                <input type="radio" name="rating" value="1" id="rating1">
                <label for="rating1">⭐</label>
            </div>
            
            <div class="form-group">
                <label class="form-label">Review</label>
                <textarea name="review_text" class="form-input" rows="5" 
                          placeholder="Tuliskan pengalaman belajar Anda dengan tutor ini..." required></textarea>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn-secondary" onclick="closeReviewModal()">Batal</button>
                <button type="submit" class="btn-primary">Kirim Review</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentFilter = 'all';

function loadBookings(filter = 'all') {
    const bookingsList = document.getElementById('bookingsList');
    bookingsList.innerHTML = '<div class="loading">Memuat data...</div>';
    
    fetch(`../../../backend/learner/get_sessions.php?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                bookingsList.innerHTML = `<div class="alert alert-error">${data.error}</div>`;
                return;
            }
            
            if (data.bookings.length === 0) {
                bookingsList.innerHTML = `
                    <div class="empty-state">
                        <h3>Belum ada booking</h3>
                        <p>Anda belum memiliki riwayat belajar. <a href="../public/landing_page.php">Cari tutor sekarang</a></p>
                    </div>
                `;
                return;
            }
            
            bookingsList.innerHTML = data.bookings.map(booking => {
                const date = new Date(booking.booking_date);
                const dateFormatted = date.toLocaleDateString('id-ID', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                const timeFormatted = booking.booking_time.substring(0, 5);
                const tutorInitial = booking.tutor_name.charAt(0).toUpperCase();
                const priceFormatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(booking.price);
                
                let actionsHTML = '';
                if (booking.status === 'completed' && !booking.has_review) {
                    actionsHTML = `
                        <button class="btn-review" onclick="openReviewModal(${booking.id})">
                            Beri Rating & Review
                        </button>
                    `;
                }
                
                return `
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div class="booking-tutor-info">
                                <div class="tutor-avatar-small">${tutorInitial}</div>
                                <div>
                                    <h3 style="margin: 0; color: var(--color-text-dark);">${booking.tutor_name}</h3>
                                    <p style="margin: 0; font-size: 12px; color: var(--color-text-light);">${booking.tutor_email}</p>
                                </div>
                            </div>
                            <span class="status-badge status-${booking.status}">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                            </span>
                        </div>
                        <div class="booking-details">
                            <p><strong>Mata Pelajaran:</strong> ${booking.subject_name}</p>
                            <p><strong>Harga:</strong> ${priceFormatted}</p>
                            <p><strong>Tanggal:</strong> ${dateFormatted}</p>
                            <p><strong>Waktu:</strong> ${timeFormatted}</p>
                            ${booking.notes ? `<p><strong>Catatan:</strong> ${booking.notes}</p>` : ''}
                            ${booking.has_review ? `
                                <div style="margin-top: 15px; padding: 10px; background: #f0f0f0; border-radius: 5px;">
                                    <p style="margin: 0;"><strong>Rating Anda:</strong> ${'⭐'.repeat(booking.rating)}</p>
                                    <p style="margin: 5px 0 0 0; font-size: 13px;">${booking.review_text}</p>
                                </div>
                            ` : ''}
                        </div>
                        ${actionsHTML ? `<div class="booking-actions">${actionsHTML}</div>` : ''}
                    </div>
                `;
            }).join('');
        })
        .catch(error => {
            bookingsList.innerHTML = `<div class="alert alert-error">Terjadi kesalahan: ${error.message}</div>`;
        });
}

function openReviewModal(bookingId) {
    document.getElementById('reviewBookingId').value = bookingId;
    document.getElementById('reviewModal').classList.add('active');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('active');
    document.getElementById('reviewForm').reset();
}

document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        loadBookings(currentFilter);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    loadBookings('all');
});

document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewModal();
    }
});
</script>

<?php require_once '../../layouts/footer.php'; ?>

