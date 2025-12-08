<?php
// Lokasi file: src/backend/helpers/EmailService.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// === LOAD PHPMailer ===
// Sesuaikan path berdasarkan struktur foldermu. Asumsi folder PhpMailer ada di .../backend/PhpMailer
require_once __DIR__ . '/../PhpMailer/PHPMailer.php';
require_once __DIR__ . '/../PhpMailer/SMTP.php';
require_once __DIR__ . '/../PhpMailer/Exception.php';

// === CONFIG (Asumsi konstanta ini didefinisikan di sini untuk kemudahan) ===

// 🔥 PERBAIKAN: SYSTEM_EMAIL_SENDER HARUS SAMA DENGAN $mail->Username
define('SYSTEM_EMAIL_SENDER', 'dhinivadilas@gmail.com'); 
define('SYSTEM_SENDER_NAME', 'SIMORA Administrator');
define('LOGIN_PAGE_URL', 'http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/frontend/auth/login.html');

class EmailService
{
    /**
     * Mengirim email notifikasi via SMTP Gmail (PHPMailer).
     */
    public static function sendApprovalNotification(string $recipientEmail, string $username, string $action): bool
    {
        if ($action === 'approved') {
            $subject = "✅ Akun SIMORA Anda Telah Disetujui!";
            $bodyHtml = self::getApprovedEmailTemplate($username);

        } elseif ($action === 'rejected') {
            $subject = "⚠️ Status Akun SIMORA Anda (Pending)";
            $bodyHtml = self::getRejectedEmailTemplate($username);

        } else {
            error_log("Unknown email action: " . $action);
            return false;
        }

        
        $mail = new PHPMailer(true);

        try {
            // CONFIG SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            // === KREDENSIAL GMAIL ===
            // Username dan Password App HARUS SESUAI
            $mail->Username = 'dhinivadilas@gmail.com'; 
            $mail->Password = 'tvxa wquo vhwh taej'; // Sandi Aplikasi (App Password)

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8'; // Tambahkan untuk encoding yang benar

            // SET HEADER (setFrom HARUS SAMA dengan $mail->Username)
            $mail->setFrom(SYSTEM_EMAIL_SENDER, SYSTEM_SENDER_NAME); // 🔥 PERBAIKAN
            $mail->addAddress($recipientEmail);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $bodyHtml;

            $mail->send();
            error_log("📧 EMAIL SENT SUCCESSFULLY to " . $recipientEmail);
            return true;

        } catch (Exception $e) {
            // Catat error PHPMailer ke log server Anda
            error_log("❌ FAILED EMAIL: {$mail->ErrorInfo}"); 
            return false;
        }
    }


    // --- TEMPLATE EMAIL ---

    /**
     * Template Email Approved
     */
    private static function getApprovedEmailTemplate(string $username): string
    {
        $loginUrl = LOGIN_PAGE_URL;
        return "
             <div style='font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;'>
                 <h2 style='color: #01A29D;'>Selamat, {$username}!</h2>
                 <p>Akun Anda telah <strong>DISETUJUI</strong> oleh Administrator.</p>
                 <p>Anda sekarang dapat login ke sistem SIMORA.</p>
                 <a href='{$loginUrl}' style='display: inline-block; padding: 10px 15px; background: #01A29D; color: white; text-decoration: none; border-radius: 5px;'>Login Sekarang</a>
                 <br><br>
                 <small style='color: #666;'>Terima kasih - Tim SIMORA</small>
             </div>
        ";
    }

    /**
     * Template Email Rejected
     */
    private static function getRejectedEmailTemplate(string $username): string
    {
        return "
             <div style='font-family: Arial, sans-serif; line-height: 1.6; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;'>
                 <h2 style='color: #FF853D;'>Halo, {$username}</h2>
                 <p>Kami telah meninjau pendaftaran Anda. Akun Anda masih berstatus <strong>PENDING / DITOLAK</strong>.</p>
                 <p>Silakan hubungi admin untuk informasi atau proses lebih lanjut.</p>
                 <small style='color: #666;'>Terima kasih - Tim SIMORA</small>
             </div>
        ";
    }
}