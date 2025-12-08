<?php
// Lokasi file: src/backend/helpers/EmailService.php

// --- KONFIGURASI EMAIL NATIVE ---
// Ganti nilai-nilai ini!
// Email ini harus valid di server hosting Anda agar fungsi mail() bekerja.
define('SYSTEM_EMAIL_SENDER', 'noreply@simora.example.com'); 
define('SYSTEM_SENDER_NAME', 'SIMORA Administrator');
// Ganti URL ini sesuai dengan path hosting Anda
define('LOGIN_PAGE_URL', 'http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/frontend/auth/login.html'); 

class EmailService
{
    /**
     * Mengirim notifikasi persetujuan atau penolakan akun menggunakan fungsi mail() native PHP.
     *
     * @param string $recipientEmail Email penerima.
     * @param string $username Nama pengguna (atau nama lengkap).
     * @param string $action 'approved' atau 'rejected'.
     * @return bool True jika email berhasil dikirim, False sebaliknya.
     */
    public static function sendApprovalNotification(string $recipientEmail, string $username, string $action): bool
    {
        $to = $recipientEmail;
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        // Header From yang benar sangat penting untuk fungsi mail()
        $headers .= "From: " . SYSTEM_SENDER_NAME . " <" . SYSTEM_EMAIL_SENDER . ">\r\n";
        $headers .= "Reply-To: " . SYSTEM_EMAIL_SENDER . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        if ($action === 'approved') {
            $subject = "✅ Akun SIMORA Anda Telah Disetujui!";
            $bodyHtml = self::getApprovedEmailTemplate($username);
        } elseif ($action === 'rejected') {
            $subject = "⚠️ Status Akun SIMORA Anda (Pending)";
            $bodyHtml = self::getRejectedEmailTemplate($username);
        } else {
            error_log("Attempted to send email with unknown action: " . $action);
            return false;
        }

        try {
            // Menggunakan fungsi mail() native PHP
            $success = mail($to, $subject, $bodyHtml, $headers);
            
            if (!$success) {
                error_log("Native mail() failed to send to: " . $recipientEmail);
            } else {
                error_log("Native mail() successfully sent approval notification to: " . $recipientEmail);
            }
            return $success;

        } catch (Exception $e) {
            error_log("Error during native mail() process: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Template Email untuk Status Disetujui (Approved).
     */
    private static function getApprovedEmailTemplate(string $username): string
    {
        $loginUrl = LOGIN_PAGE_URL;
        
        return "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #01A29D;'>Selamat, $username!</h2>
                <p>Akun keanggotaan SIMORA Anda telah **disetujui** oleh Administrator.</p>
                <p>Anda sekarang dapat **login** dan mengakses semua fitur dan sumber daya di dashboard anggota.</p>
                <p style='margin-top: 30px; text-align: center;'>
                    <a href='{$loginUrl}' style='display: inline-block; padding: 12px 25px; background-color: #01A29D; color: white !important; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                        Masuk ke Dashboard
                    </a>
                </p>
                <p style='margin-top: 30px; font-size: 0.9em; color: #666;'>Jika Anda memiliki pertanyaan, silakan hubungi tim dukungan kami.</p>
                <p>Terima kasih,<br>Tim Administrator SIMORA</p>
            </div>
        ";
    }
    
    /**
     * Template Email untuk Status Ditolak/Pending (Rejected).
     */
    private static function getRejectedEmailTemplate(string $username): string
    {
        return "
            <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #FF853D;'>Halo $username,</h2>
                <p>Kami telah memproses permintaan persetujuan akun SIMORA Anda.</p>
                <p>Saat ini, akun Anda masih berstatus **Pending** atau telah ditolak oleh Admin. Kami menemukan bahwa ada informasi yang kurang atau tidak sesuai dengan persyaratan keanggotaan.</p>
                <p>Silakan hubungi Administrator untuk menyelesaikan pendaftaran Anda.</p>
                <p>Terima kasih atas pengertian Anda.</p>
            </div>
        ";
    }
}