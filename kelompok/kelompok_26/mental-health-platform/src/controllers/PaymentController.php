<?php
// src/controllers/PaymentController.php

require_once __DIR__ . '/../config/database.php';
// Karena Payment Model belum dibuat, kita berinteraksi langsung dengan $conn.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class PaymentController {
    protected $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Menangani unggah bukti pembayaran ke database dan penyimpanan file.
     */
    public function uploadProof() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Metode request tidak valid.';
            header('Location: ?p=payments');
            exit;
        }
        
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = 'Anda harus login terlebih dahulu.';
            header('Location: ?p=login');
            exit;
        }
        
        $userId = intval($_SESSION['user']['user_id'] ?? $_SESSION['user']['id'] ?? 0);
        // amount opsional; jangan blokir jika tidak dikirim
        $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
        $file = $_FILES['proof_image'] ?? null;
        
        // --- Validasi Input & File ---
        if ($userId <= 0) {
            $_SESSION['error'] = 'Data pengguna tidak valid.';
            header('Location: ?p=payments');
            exit;
        }
        
        if (!$file || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            $_SESSION['error'] = 'Bukti transfer harus diunggah.';
            header('Location: ?p=payments');
            exit;
        }
        
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; 
        
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'Ukuran file terlalu besar (max 5MB).';
            header('Location: ?p=payments');
            exit;
        }
        
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = 'Tipe file tidak diperbolehkan. Gunakan JPG atau PNG.';
            header('Location: ?p=payments');
            exit;
        }
        
        // --- Proses Upload File ---
        $uploadDir = __DIR__ . '/../../uploads/payment_proofs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'proof_' . $userId . '_' . time() . '.' . strtolower($extension);
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $_SESSION['error'] = 'Gagal menyimpan file bukti transfer.';
            header('Location: ?p=payments');
            exit;
        }

        // --- Simpan ke Database ---
        // Coba insert dengan kolom amount & proof_image; jika gagal karena schema beda, fallback minimal.
        $saved = false;

        $stmt = $this->db->prepare("
            INSERT INTO payment (user_id, amount, proof_image, status, created_at)
            VALUES (?, ?, ?, 'approved', NOW())
        ");

        if ($stmt) {
            $stmt->bind_param("iis", $userId, $amount, $filename);
            $saved = $stmt->execute();
            $stmt->close();
        }

        if (!$saved) {
            // Fallback: tanpa kolom amount
            $fallback = $this->db->prepare("
                INSERT INTO payment (user_id, proof_image, status, created_at)
                VALUES (?, ?, 'approved', NOW())
            ");
            if ($fallback) {
                $fallback->bind_param("is", $userId, $filename);
                $saved = $fallback->execute();
                $fallback->close();
            }
        }

        if ($saved) {
            $_SESSION['success'] = 'Bukti pembayaran diterima dan langganan diaktifkan.';
        } else {
            $_SESSION['error'] = 'Gagal menyimpan data pembayaran ke database.';
            unlink($filepath);
        }
        
        header('Location: ?p=payments');
        exit;
    }
}