<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'learner') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

$tutor_id = isset($_GET['tutor_id']) ? intval($_GET['tutor_id']) : 0;
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

$tutor_data = null;
$subject_data = null;
$subjects_list = [];

if ($tutor_id > 0) {
    $tutor_query = "SELECT id, name, email FROM users WHERE id = '$tutor_id' AND role = 'tutor' AND status = 'active'";
    $tutor_result = mysqli_query($conn, $tutor_query);
    if ($tutor_result && mysqli_num_rows($tutor_result) > 0) {
        $tutor_data = mysqli_fetch_assoc($tutor_result);
        
        $subjects_query = "SELECT id, subject_name, price, description FROM subjects WHERE tutor_id = '$tutor_id'";
        $subjects_result = mysqli_query($conn, $subjects_query);
        while ($row = mysqli_fetch_assoc($subjects_result)) {
            $subjects_list[] = $row;
        }
    }
}

if ($subject_id > 0) {
    $subject_query = "SELECT id, tutor_id, subject_name, price, description FROM subjects WHERE id = '$subject_id'";
    $subject_result = mysqli_query($conn, $subject_query);
    if ($subject_result && mysqli_num_rows($subject_result) > 0) {
        $subject_data = mysqli_fetch_assoc($subject_result);
        if (!$tutor_data || $tutor_data['id'] != $subject_data['tutor_id']) {
            $tutor_id = $subject_data['tutor_id'];
            $tutor_query = "SELECT id, name, email FROM users WHERE id = '$tutor_id'";
            $tutor_result = mysqli_query($conn, $tutor_query);
            if ($tutor_result) {
                $tutor_data = mysqli_fetch_assoc($tutor_result);
            }
        }
    }
}

$base_url_assets = "../../assets";
require_once '../../layouts/header.php';
?>

<style>
.booking-container {
    padding: 30px 0;
    max-width: 800px;
    margin: 0 auto;
}

.booking-header {
    text-align: center;
    margin-bottom: 30px;
}

.booking-header h1 {
    color: var(--color-text-dark);
    margin-bottom: 10px;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
    position: relative;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--color-border);
    z-index: 0;
}

.step {
    background: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--color-border);
    position: relative;
    z-index: 1;
    font-weight: bold;
}

.step.active {
    background: var(--color-primary);
    color: white;
    border-color: var(--color-primary);
}

.step.completed {
    background: var(--color-success);
    color: white;
    border-color: var(--color-success);
}

.step-content {
    display: none;
}

.step-content.active {
    display: block;
}

.booking-card {
    background: white;
    padding: 30px;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    margin-bottom: 20px;
}

.tutor-info {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: var(--color-bg-light);
    border-radius: var(--border-radius);
    margin-bottom: 20px;
}

.tutor-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
}

.subject-select {
    margin-bottom: 20px;
}

.subject-option {
    padding: 15px;
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius);
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.subject-option:hover {
    border-color: var(--color-primary);
    background: #f0f7ff;
}

.subject-option.selected {
    border-color: var(--color-primary);
    background: #e3f2fd;
}

.subject-option input[type="radio"] {
    margin-right: 10px;
}

.confirmation-details {
    background: var(--color-bg-light);
    padding: 20px;
    border-radius: var(--border-radius);
    margin-bottom: 20px;
}

.confirmation-details p {
    margin: 10px 0;
    display: flex;
    justify-content: space-between;
}

.confirmation-details strong {
    color: var(--color-text-dark);
}

.btn-group {
    display: flex;
    gap: 10px;
    justify-content: space-between;
}

.btn-secondary {
    padding: 12px 24px;
    background: var(--color-bg-light);
    color: var(--color-text-dark);
    border: 2px solid var(--color-border);
    border-radius: var(--border-radius);
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: var(--color-border);
}
</style>

<div class="booking-container">
    <div class="container">
        <div class="booking-header">
            <h1>Booking Tutor</h1>
            <p>Isi form berikut untuk memesan sesi belajar</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <?php
                $error = $_GET['error'];
                if ($error == 'empty_time') echo "Harap pilih waktu booking.";
                elseif ($error == 'invalid_date') echo "Format tanggal tidak valid.";
                elseif ($error == 'past_date') echo "Tanggal tidak boleh di masa lalu.";
                elseif ($error == 'db_error') echo "Terjadi kesalahan database. Silakan coba lagi.";
                else echo "Terjadi kesalahan. Silakan coba lagi.";
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$tutor_data): ?>
            <div class="alert alert-error">
                Tutor tidak ditemukan. <a href="../public/landing_page.php">Kembali ke beranda</a>
            </div>
        <?php else: ?>
            <form id="bookingForm" action="../../../backend/learner/booking_process.php" method="POST">
                <input type="hidden" name="tutor_id" value="<?php echo $tutor_data['id']; ?>">
                
                <div class="step-content active" id="step1">
                    <div class="booking-card">
                        <h2 style="margin-bottom: 20px;">Pilih Mata Pelajaran</h2>
                        
                        <div class="tutor-info">
                            <div class="tutor-avatar">
                                <?php echo strtoupper(substr($tutor_data['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <strong><?php echo htmlspecialchars($tutor_data['name']); ?></strong>
                                <p style="font-size: 14px; color: var(--color-text-light); margin: 0;">
                                    <?php echo htmlspecialchars($tutor_data['email']); ?>
                                </p>
                            </div>
                        </div>

                        <div class="subject-select">
                            <?php if (empty($subjects_list)): ?>
                                <p>Tutor ini belum memiliki mata pelajaran yang tersedia.</p>
                            <?php else: ?>
                                <?php foreach ($subjects_list as $subject): ?>
                                    <div class="subject-option <?php echo ($subject_id == $subject['id']) ? 'selected' : ''; ?>">
                                        <label style="cursor: pointer; width: 100%; display: block;">
                                            <input type="radio" name="subject_id" value="<?php echo $subject['id']; ?>" 
                                                   <?php echo ($subject_id == $subject['id']) ? 'checked' : ''; ?> required>
                                            <strong><?php echo htmlspecialchars($subject['subject_name']); ?></strong>
                                            <span style="float: right; color: var(--color-primary); font-weight: bold;">
                                                Rp <?php echo number_format($subject['price'], 0, ',', '.'); ?>
                                            </span>
                                            <?php if (!empty($subject['description'])): ?>
                                                <p style="font-size: 14px; color: var(--color-text-light); margin: 5px 0 0 25px;">
                                                    <?php echo htmlspecialchars($subject['description']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="btn-group">
                            <a href="../public/landing_page.php" class="btn-secondary">Batal</a>
                            <button type="button" class="btn-primary" onclick="nextStep(2)">Lanjut</button>
                        </div>
                    </div>
                </div>

                <div class="step-content" id="step2">
                    <div class="booking-card">
                        <h2 style="margin-bottom: 20px;">Pilih Tanggal & Waktu</h2>
                        
                        <div class="form-group">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="booking_date" class="form-input" required 
                                   min="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Waktu</label>
                            <input type="time" name="booking_time" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-input" rows="4" 
                                      placeholder="Contoh: Saya ingin fokus pada materi Aljabar..."></textarea>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn-secondary" onclick="prevStep(1)">Kembali</button>
                            <button type="button" class="btn-primary" onclick="nextStep(3)">Lanjut</button>
                        </div>
                    </div>
                </div>

                <div class="step-content" id="step3">
                    <div class="booking-card">
                        <h2 style="margin-bottom: 20px;">Konfirmasi Booking</h2>
                        
                        <div class="confirmation-details" id="confirmationDetails">
                            <!-- Akan diisi oleh JavaScript -->
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn-secondary" onclick="prevStep(2)">Kembali</button>
                            <button type="submit" class="btn-primary">Konfirmasi & Booking</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;

function updateStepIndicator() {
    for (let i = 1; i <= totalSteps; i++) {
        const stepEl = document.querySelector(`.step-indicator .step:nth-child(${i})`);
        if (i < currentStep) {
            stepEl.classList.remove('active');
            stepEl.classList.add('completed');
        } else if (i === currentStep) {
            stepEl.classList.add('active');
            stepEl.classList.remove('completed');
        } else {
            stepEl.classList.remove('active', 'completed');
        }
    }
}

function showStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.getElementById(`step${step}`).classList.add('active');
    currentStep = step;
    updateStepIndicator();
    
    if (step === 3) {
        updateConfirmation();
    }
}

function nextStep(step) {
    if (step === 2) {
        const subjectId = document.querySelector('input[name="subject_id"]:checked');
        if (!subjectId) {
            alert('Harap pilih mata pelajaran terlebih dahulu');
            return;
        }
    }
    if (step === 3) {
        const date = document.querySelector('input[name="booking_date"]').value;
        const time = document.querySelector('input[name="booking_time"]').value;
        if (!date || !time) {
            alert('Harap isi tanggal dan waktu terlebih dahulu');
            return;
        }
    }
    showStep(step);
}

function prevStep(step) {
    showStep(step);
}

function updateConfirmation() {
    const form = document.getElementById('bookingForm');
    const formData = new FormData(form);
    
    const tutorName = '<?php echo htmlspecialchars($tutor_data['name'] ?? ''); ?>';
    const subjectId = formData.get('subject_id');
    const subjectName = document.querySelector(`input[name="subject_id"][value="${subjectId}"]`)?.parentElement.querySelector('strong')?.textContent || '';
    const subjectPrice = document.querySelector(`input[name="subject_id"][value="${subjectId}"]`)?.parentElement.querySelector('span')?.textContent || '';
    const date = formData.get('booking_date');
    const time = formData.get('booking_time');
    const notes = formData.get('notes');
    
    const dateObj = new Date(date);
    const dateFormatted = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    document.getElementById('confirmationDetails').innerHTML = `
        <p><strong>Tutor:</strong> <span>${tutorName}</span></p>
        <p><strong>Mata Pelajaran:</strong> <span>${subjectName}</span></p>
        <p><strong>Harga:</strong> <span>${subjectPrice}</span></p>
        <p><strong>Tanggal:</strong> <span>${dateFormatted}</span></p>
        <p><strong>Waktu:</strong> <span>${time}</span></p>
        ${notes ? `<p><strong>Catatan:</strong> <span>${notes}</span></p>` : ''}
    `;
}

document.addEventListener('DOMContentLoaded', function() {
    const stepIndicator = document.createElement('div');
    stepIndicator.className = 'step-indicator';
    stepIndicator.innerHTML = `
        <div class="step active">1</div>
        <div class="step">2</div>
        <div class="step">3</div>
    `;
    document.querySelector('.booking-header').after(stepIndicator);
    updateStepIndicator();
});
</script>

<?php require_once '../../layouts/footer.php'; ?>

