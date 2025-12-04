<?php

class Transaction extends Model
{
    // Fungsi untuk membuat Struk (Header)
    public function create($data)
    {
        // Pastikan session user id tersedia
        $userId = $_SESSION['user']['id'];
        $total = $data['total'];

        $sql = "INSERT INTO transactions (user_id, total_amount) VALUES (:user_id, :total_amount)";
        
        // Gunakan prepare dan execute (PDO Standar)
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':total_amount' => $total
        ]);
        
        return $this->db->lastInsertId();
    }

    // Fungsi untuk menyimpan detail barang
    public function createDetail($transId, $item)
    {
        $subtotal = $item['price'] * $item['qty'];

        $sql = "INSERT INTO transaction_details (transaction_id, product_id, qty, subtotal) 
                VALUES (:tid, :pid, :qty, :sub)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':tid' => $transId,
            ':pid' => $item['id'],
            ':qty' => $item['qty'],
            ':sub' => $subtotal
        ]);
    }

    // Ambil data transaksi untuk dicetak
    public function getTransactionById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Ambil list barang untuk dicetak
    public function getTransactionItems($id)
    {
        $sql = "SELECT td.*, p.name, p.price 
                FROM transaction_details td 
                JOIN products p ON td.product_id = p.id 
                WHERE td.transaction_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }
}