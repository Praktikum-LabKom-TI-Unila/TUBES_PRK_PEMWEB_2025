<?php

declare(strict_types=1);

final class Emailer
{
    /**
     * Mengirim link reset password menggunakan fungsi mail() bawaan PHP.
     * * @param string $recipientEmail Email tujuan.
     * @param string $recipientName Nama pengguna.
     * @param string $resetLink URL link reset password.
     * @return bool True jika email berhasil dikirim, false jika gagal.
     */
    public function sendResetPasswordEmail(string $recipientEmail, string $recipientName, string $resetLink): bool
    {
        $to = $recipientEmail;
        $subject = 'Reset Password Akun myUnila Lost & Found';
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: myUnila Lost & Found <no-reply@myunila.ac.id>\r\n";
        $headers .= "Reply-To: no-reply@myunila.ac.id\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        $message = $this->getResetEmailBody($recipientName, $resetLink);
        
        $mailSent = mail($to, $subject, $message, $headers);
        
        return $mailSent;
    }

    private function getResetEmailBody(string $name, string $resetLink): string
    {
        return "
            <!DOCTYPE html>
            <html lang='id'>
            <body style='font-family: sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;'>
                <div style='background-color: #f4f4f4; padding: 20px 0;'>
                    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); overflow: hidden;'>
                        
                        <div style='background-color: #0ea5e9; color: white; padding: 20px; text-align: center;'>
                            <h1 style='margin: 0; font-size: 24px;'>myUnila Lost & Found</h1>
                            <p style='margin: 5px 0 0; font-size: 14px;'>Pusat Bantuan Kampus</p>
                        </div>

                        <div style='padding: 30px;'>
                            <p>Halo, <strong>" . htmlspecialchars($name) . "</strong>,</p>
                            <p>Kami menerima permintaan reset password untuk akun Anda. Untuk melanjutkan, silakan klik tombol di bawah ini:</p>
                            
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='" . htmlspecialchars($resetLink) . "' style='display: inline-block; padding: 12px 25px; background-color: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);'>
                                    Reset Password Anda
                                </a>
                            </div>
                            
                            <p>Link ini akan kadaluarsa dalam 1 jam demi keamanan akun Anda.</p>
                            <p>Jika Anda tidak mengajukan permintaan ini, Anda bisa mengabaikan email ini. Password Anda akan tetap aman.</p>
                            
                            <p style='margin-top: 30px; font-size: 14px; color: #777;'>Terima kasih,</p>
                            <p style='font-size: 14px; color: #777; margin: 0;'>Tim myUnila Lost & Found</p>
                            
                            <p style='font-size: 12px; word-break: break-all; margin-top: 20px; color: #999;'>Link alternatif: <a href='" . htmlspecialchars($resetLink) . "'>" . htmlspecialchars($resetLink) . "</a></p>
                        </div>
                        
                        <div style='background-color: #f0f0f0; padding: 15px; text-align: center; font-size: 11px; color: #777;'>
                            <p style='margin: 0;'>Ini adalah email otomatis. Mohon tidak membalas email ini.</p>
                            <p style='margin: 5px 0 0;'>&copy; " . date('Y') . " myUnila Lost & Found. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}