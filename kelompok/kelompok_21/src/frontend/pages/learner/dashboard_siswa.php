<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'learner') {
    header("Location: ../auth/login.php?error=unauthorized");
    exit();
}

$learner_id = $_SESSION['user_id'];

$stats_query = "SELECT 
    COUNT(*) as total_booking,
    SUM(CASE WHEN status IN ('pending', 'confirmed') THEN 1 ELSE 0 END) as active_booking,
    (SELECT COUNT(*) FROM reviews WHERE learner_id = '$learner_id') as total_reviews
FROM bookings 
WHERE learner_id = '$learner_id'";

$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$recent_query = "SELECT 
    b.id,
    b.booking_date,
    b.booking_time,
    b.status,
    u.name as tutor_name,
    s.subject_name,
    s.price
FROM bookings b
INNER JOIN users u ON b.tutor_id = u.id
INNER JOIN subjects s ON b.subject_id = s.id
WHERE b.learner_id = '$learner_id'
ORDER BY b.created_at DESC
LIMIT 5";

$recent_result = mysqli_query($conn, $recent_query);

$upcoming_query = "SELECT 
    b.id,
    b.booking_date,
    b.booking_time,
    b.status,
    u.name as tutor_name,
    s.subject_name,
    s.price
FROM bookings b
INNER JOIN users u ON b.tutor_id = u.id
INNER JOIN subjects s ON b.subject_id = s.id
WHERE b.learner_id = '$learner_id' 
    AND b.status IN ('pending', 'confirmed')
    AND (b.booking_date > CURDATE() OR (b.booking_date = CURDATE() AND b.booking_time > CURTIME()))
ORDER BY b.booking_date ASC, b.booking_time ASC
LIMIT 1";

$upcoming_result = mysqli_query($conn, $upcoming_query);
$upcoming_booking = mysqli_fetch_assoc($upcoming_result);

$base_url_assets = "../../assets";
require_once '../../layouts/header.php';
?>

<style>
.dashboard-container {
    padding: 40px 0;
    min-height: calc(100vh - 200px);
}

.dashboard-header {
    margin-bottom: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.dashboard-header-content h1 {
    color: var(--color-text-dark);
    margin-bottom: 8px;
    font-size: 32px;
    font-weight: 700;
}

.dashboard-header-content p {
    color: var(--color-text-light);
    font-size: 16px;
}

.welcome-badge {
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-top: 4px solid;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-card.stat-primary {
    border-top-color: #1a73e8;
}

.stat-card.stat-success {
    border-top-color: #00b894;
}

.stat-card.stat-warning {
    border-top-color: #ffc107;
}

.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-card.stat-primary .stat-icon {
    background: #e3f2fd;
    color: #1a73e8;
}

.stat-card.stat-success .stat-icon {
    background: #e0f7f4;
    color: #00b894;
}

.stat-card.stat-warning .stat-icon {
    background: #fff9e6;
    color: #ffc107;
}

.stat-card h3 {
    font-size: 13px;
    color: var(--color-text-light);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.stat-card .stat-value {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 5px;
    line-height: 1;
}

.stat-card.stat-primary .stat-value {
    color: #1a73e8;
}

.stat-card.stat-success .stat-value {
    color: #00b894;
}

.stat-card.stat-warning .stat-value {
    color: #ffc107;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.btn-action {
    padding: 18px 30px;
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
}

.btn-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.btn-action.secondary {
    background: white;
    color: var(--color-primary);
    border: 2px solid var(--color-primary);
    box-shadow: 0 2px 10px rgba(26, 115, 232, 0.1);
}

.btn-action.secondary:hover {
    background: var(--color-primary);
    color: white;
}

.recent-bookings {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.recent-bookings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.recent-bookings h2 {
    color: var(--color-text-dark);
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.view-all-link {
    color: var(--color-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.3s;
}

.view-all-link:hover {
    color: var(--color-primary-dark);
}

.booking-item {
    padding: 20px;
    border-bottom: 1px solid var(--color-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s;
    border-radius: 8px;
    margin-bottom: 10px;
}

.booking-item:hover {
    background: var(--color-bg-light);
}

.booking-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.booking-info {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 1;
}

.tutor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    font-weight: bold;
    flex-shrink: 0;
}

.booking-details h4 {
    margin-bottom: 5px;
    color: var(--color-text-dark);
    font-size: 16px;
}

.booking-details p {
    color: var(--color-text-light);
    font-size: 14px;
    margin: 3px 0;
}

.booking-price {
    color: var(--color-primary);
    font-weight: 600;
    font-size: 14px;
}

.booking-meta {
    display: flex;
    align-items: center;
    gap: 15px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

.alert {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--color-text-light);
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    color: var(--color-text-dark);
    margin-bottom: 10px;
    font-size: 20px;
}

.empty-state a {
    color: var(--color-primary);
    font-weight: 600;
    text-decoration: none;
}

.empty-state a:hover {
    text-decoration: underline;
}

.upcoming-booking-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 40px;
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.upcoming-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.upcoming-icon {
    font-size: 32px;
    background: rgba(255, 255, 255, 0.2);
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upcoming-header h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
}

.upcoming-subtitle {
    margin: 5px 0 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.upcoming-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    align-items: start;
}

.upcoming-tutor {
    display: flex;
    align-items: center;
    gap: 15px;
}

.upcoming-tutor .tutor-avatar-small {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.upcoming-tutor h4 {
    margin: 0 0 5px 0;
    font-size: 18px;
}

.upcoming-tutor p {
    margin: 0;
    opacity: 0.9;
    font-size: 14px;
}

.upcoming-datetime {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.datetime-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.datetime-item:last-child {
    border-bottom: none;
}

.datetime-label {
    font-size: 14px;
    opacity: 0.9;
}

.datetime-value {
    font-weight: 600;
    font-size: 15px;
}

.datetime-value.highlight {
    background: rgba(255, 255, 255, 0.2);
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 700;
}

.upcoming-status {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 10px;
}

.upcoming-status .status-badge {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.quick-tips {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 40px;
    border-left: 4px solid var(--color-secondary);
}

.quick-tips h3 {
    margin: 0 0 15px 0;
    color: var(--color-text-dark);
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    padding: 10px 0;
    color: var(--color-text-light);
    font-size: 14px;
    display: flex;
    align-items: start;
    gap: 10px;
}

.tips-list li::before {
    content: '💡';
    font-size: 16px;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .booking-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .booking-meta {
        width: 100%;
        justify-content: space-between;
    }
    
    .upcoming-content {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="dashboard-container">
    <div class="container">
        <div class="dashboard-header">
            <div class="dashboard-header-content">
                <h1>Dashboard Siswa</h1>
                <p>Selamat datang kembali, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>! 👋</p>
            </div>
            <div class="welcome-badge">
                🎓 Learner
            </div>
        </div>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'booking_success'): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <span>Booking berhasil dibuat! Menunggu konfirmasi dari tutor.</span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span>
                    <?php
                    $error = $_GET['error'];
                    if ($error == 'empty_fields') echo "Harap isi semua field yang diperlukan.";
                    else echo "Terjadi kesalahan. Silakan coba lagi.";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-card-header">
                    <div>
                        <h3>Total Booking</h3>
                        <div class="stat-value"><?php echo $stats['total_booking'] ?? 0; ?></div>
                    </div>
                    <div class="stat-icon">📚</div>
                </div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-card-header">
                    <div>
                        <h3>Booking Aktif</h3>
                        <div class="stat-value"><?php echo $stats['active_booking'] ?? 0; ?></div>
                    </div>
                    <div class="stat-icon">✅</div>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-card-header">
                    <div>
                        <h3>Rating Diberikan</h3>
                        <div class="stat-value"><?php echo $stats['total_reviews'] ?? 0; ?></div>
                    </div>
                    <div class="stat-icon">⭐</div>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="../public/landing_page.php" class="btn-action">
                <span>🔍</span>
                <span>Cari Tutor</span>
            </a>
            <a href="riwayat.php" class="btn-action secondary">
                <span>📋</span>
                <span>Lihat Riwayat</span>
            </a>
        </div>

        <?php if ($upcoming_booking): 
            $upcoming_date = new DateTime($upcoming_booking['booking_date']);
            $upcoming_time = date('H:i', strtotime($upcoming_booking['booking_time']));
            $upcoming_datetime = new DateTime($upcoming_booking['booking_date'] . ' ' . $upcoming_booking['booking_time']);
            $now = new DateTime();
            $diff = $now->diff($upcoming_datetime);
            $days_left = $diff->days;
            $hours_left = $diff->h;
        ?>
            <div class="upcoming-booking-card">
                <div class="upcoming-header">
                    <div class="upcoming-icon">⏰</div>
                    <div>
                        <h3>Sesi Berikutnya</h3>
                        <p class="upcoming-subtitle">Jangan lupa jadwal belajar Anda!</p>
                    </div>
                </div>
                <div class="upcoming-content">
                    <div class="upcoming-tutor">
                        <div class="tutor-avatar-small"><?php echo strtoupper(substr($upcoming_booking['tutor_name'], 0, 1)); ?></div>
                        <div>
                            <h4><?php echo htmlspecialchars($upcoming_booking['tutor_name']); ?></h4>
                            <p><?php echo htmlspecialchars($upcoming_booking['subject_name']); ?></p>
                        </div>
                    </div>
                    <div class="upcoming-datetime">
                        <div class="datetime-item">
                            <span class="datetime-label">📅 Tanggal</span>
                            <span class="datetime-value"><?php echo $upcoming_date->format('d M Y'); ?></span>
                        </div>
                        <div class="datetime-item">
                            <span class="datetime-label">🕐 Waktu</span>
                            <span class="datetime-value"><?php echo $upcoming_time; ?> WIB</span>
                        </div>
                        <div class="datetime-item">
                            <span class="datetime-label">⏳ Waktu Tersisa</span>
                            <span class="datetime-value highlight">
                                <?php 
                                if ($days_left > 0) {
                                    echo $days_left . ' hari lagi';
                                } elseif ($hours_left > 0) {
                                    echo $hours_left . ' jam lagi';
                                } else {
                                    echo 'Segera dimulai!';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="upcoming-status">
                        <span class="status-badge status-<?php echo $upcoming_booking['status']; ?>">
                            <?php echo $upcoming_booking['status'] == 'pending' ? 'Menunggu Konfirmasi' : 'Terkonfirmasi'; ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="quick-tips">
            <h3>
                <span>💡</span>
                <span>Tips Belajar Efektif</span>
            </h3>
            <ul class="tips-list">
                <li>Siapkan pertanyaan sebelum sesi dimulai</li>
                <li>Review materi sebelumnya untuk memaksimalkan waktu belajar</li>
                <li>Catat poin-poin penting selama sesi berlangsung</li>
            </ul>
        </div>

        <div class="recent-bookings">
            <div class="recent-bookings-header">
                <h2>Booking Terbaru</h2>
                <a href="riwayat.php" class="view-all-link">Lihat Semua →</a>
            </div>
            <?php if (mysqli_num_rows($recent_result) > 0): ?>
                <?php while ($booking = mysqli_fetch_assoc($recent_result)): 
                    $tutor_initial = strtoupper(substr($booking['tutor_name'], 0, 1));
                    $date = new DateTime($booking['booking_date']);
                    $date_formatted = $date->format('d M Y');
                    $time_formatted = date('H:i', strtotime($booking['booking_time']));
                    $price_formatted = 'Rp ' . number_format($booking['price'], 0, ',', '.');
                ?>
                    <div class="booking-item">
                        <div class="booking-info">
                            <div class="tutor-avatar"><?php echo $tutor_initial; ?></div>
                            <div class="booking-details">
                                <h4><?php echo htmlspecialchars($booking['tutor_name']); ?></h4>
                                <p><?php echo htmlspecialchars($booking['subject_name']); ?></p>
                                <p>
                                    <span>📅 <?php echo $date_formatted; ?></span>
                                    <span style="margin: 0 8px;">•</span>
                                    <span>🕐 <?php echo $time_formatted; ?></span>
                                </p>
                                <p class="booking-price"><?php echo $price_formatted; ?></p>
                            </div>
                        </div>
                        <div class="booking-meta">
                            <span class="status-badge status-<?php echo $booking['status']; ?>">
                                <?php 
                                $status_text = [
                                    'pending' => 'Menunggu',
                                    'confirmed' => 'Dikonfirmasi',
                                    'completed' => 'Selesai',
                                    'cancelled' => 'Dibatalkan'
                                ];
                                echo $status_text[$booking['status']] ?? ucfirst($booking['status']);
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📝</div>
                    <h3>Belum ada booking</h3>
                    <p>Mulai perjalanan belajar Anda dengan mencari tutor yang tepat!</p>
                    <p style="margin-top: 15px;">
                        <a href="../public/landing_page.php">Cari tutor sekarang →</a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>

