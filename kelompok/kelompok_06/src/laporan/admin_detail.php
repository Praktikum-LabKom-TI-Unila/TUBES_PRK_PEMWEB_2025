<?php
/**
 * File: admin_detail.php
 * Deskripsi: Halaman detail laporan untuk admin dengan fitur update status
 * Anggota: Nabila Salwa (Anggota 3)
 */

// Memulai session
session_start();

// TEMPORARY: Disable login check for testing
// Set dummy admin session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['nama'] = 'Admin';
    $_SESSION['email'] = 'admin@silatium.com';
    $_SESSION['role'] = 'admin';
}

// Include file koneksi database
require_once('../config/koneksi.php');

// Ambil ID laporan dari URL
$laporan_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($laporan_id <= 0) {
    header("Location: admin_list.php?status=error&message=" . urlencode("ID laporan tidak valid"));
    exit();
}

// Proses update status jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    
    // Validasi status
    $valid_statuses = ['pending', 'diproses', 'selesai', 'ditolak'];
    if (!in_array($new_status, $valid_statuses)) {
        $error_message = "Status tidak valid";
    } else {
        // Update status menggunakan prepared statement
        $update_query = "UPDATE laporan SET status = ? WHERE id = ?";
        
        if ($stmt = mysqli_prepare($conn, $update_query)) {
            mysqli_stmt_bind_param($stmt, "si", $new_status, $laporan_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Status laporan berhasil diperbarui";
                // Redirect to refresh data
                header("Location: admin_detail.php?id=" . $laporan_id . "&success=1");
                exit();
            } else {
                $error_message = "Gagal memperbarui status laporan";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            $error_message = "Gagal mempersiapkan query";
        }
    }
}

// Query untuk mengambil detail laporan dengan informasi user
$query = "SELECT l.*, u.nama as nama_user, u.email as email_user 
          FROM laporan l 
          LEFT JOIN users u ON l.user_id = u.id 
          WHERE l.id = ?";

$laporan = null;

if ($stmt = mysqli_prepare($conn, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $laporan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $laporan = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
}

// Jika laporan tidak ditemukan
if (!$laporan) {
    mysqli_close($conn);
    header("Location: admin_list.php?status=error&message=" . urlencode("Laporan tidak ditemukan"));
    exit();
}

// Tutup koneksi
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Admin SILATIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: rgba(0, 0, 0, 0.5);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Modal Dialog */
        .modal {
            background: white;
            border-radius: 12px;
            max-width: 560px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }

        .close-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .close-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .modal-body {
            padding: 24px;
        }

        .detail-section {
            margin-bottom: 20px;
        }

        .detail-label {
            font-size: 12px;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 15px;
            color: #0f172a;
            font-weight: 500;
        }

        .detail-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
        }

        .detail-date {
            font-size: 14px;
            color: #64748b;
        }

        /* Status Update Section */
        .status-update {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .status-update-label {
            font-size: 12px;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-buttons {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .status-btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-btn.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-btn.pending:hover,
        .status-btn.pending.active {
            background: #fde68a;
        }

        .status-btn.diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-btn.diproses:hover,
        .status-btn.diproses.active {
            background: #bfdbfe;
        }

        .status-btn.selesai {
            background: #d1fae5;
            color: #065f46;
        }

        .status-btn.selesai:hover,
        .status-btn.selesai.active {
            background: #a7f3d0;
        }

        .status-btn.ditolak {
            background: #fecaca;
            color: #991b1b;
        }

        .status-btn.ditolak:hover,
        .status-btn.ditolak.active {
            background: #fca5a5;
        }

        .status-btn.active {
            box-shadow: 0 0 0 2px white, 0 0 0 4px currentColor;
        }

        /* Footer Buttons */
        .modal-footer {
            padding: 0 24px 24px;
            display: flex;
            justify-content: flex-end;
        }

        .btn-close {
            padding: 10px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .modal {
                margin: 20px;
            }

            .status-buttons {
                flex-wrap: wrap;
            }

            .status-btn {
                flex-basis: calc(50% - 4px);
            }
        }
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Detail Laporan</h2>
            <button class="close-btn" onclick="window.location.href='admin_list.php'">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <!-- User Email -->
            <div class="detail-section">
                <div class="detail-label">User</div>
                <div class="detail-value"><?php echo htmlspecialchars($laporan['email_user'] ?? 'N/A'); ?></div>
            </div>

            <!-- Judul Laporan -->
            <div class="detail-section">
                <div class="detail-label">Judul Laporan</div>
                <div class="detail-value"><?php echo htmlspecialchars($laporan['judul_laporan']); ?></div>
            </div>

            <!-- Isi Laporan -->
            <div class="detail-section">
                <div class="detail-label">Isi Laporan</div>
                <div class="detail-text"><?php echo nl2br(htmlspecialchars($laporan['isi_laporan'])); ?></div>
            </div>

            <!-- Tanggal Laporan -->
            <div class="detail-section">
                <div class="detail-label">Tanggal Laporan</div>
                <div class="detail-date"><?php echo date('d F Y', strtotime($laporan['tanggal'])); ?></div>
            </div>

            <!-- Update Status -->
            <div class="status-update">
                <div class="status-update-label">Update Status</div>
                <form method="POST" id="statusForm">
                    <div class="status-buttons">
                        <button type="button" class="status-btn pending <?php echo $laporan['status'] === 'pending' ? 'active' : ''; ?>" onclick="selectStatus('pending')">
                            Pending
                        </button>
                        <button type="button" class="status-btn diproses <?php echo $laporan['status'] === 'diproses' ? 'active' : ''; ?>" onclick="selectStatus('diproses')">
                            Diproses
                        </button>
                        <button type="button" class="status-btn selesai <?php echo $laporan['status'] === 'selesai' ? 'active' : ''; ?>" onclick="selectStatus('selesai')">
                            Selesai
                        </button>
                    </div>
                    <input type="hidden" name="status" id="statusInput" value="<?php echo $laporan['status']; ?>">
                    <input type="hidden" name="update_status" value="1">
                </form>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-close" onclick="window.location.href='admin_list.php'">Tutup</button>
        </div>
    </div>

    <script>
        function selectStatus(status) {
            // Remove active class from all buttons
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to selected button
            event.target.classList.add('active');
            
            // Update hidden input
            document.getElementById('statusInput').value = status;
            
            // Submit form automatically
            document.getElementById('statusForm').submit();
        }
    </script>
</body>
</html>