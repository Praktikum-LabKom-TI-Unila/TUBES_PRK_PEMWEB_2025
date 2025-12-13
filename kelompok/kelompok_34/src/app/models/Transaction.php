<?php

class Transaction extends Model
{
    // Fungsi untuk membuat Struk (Header)
    // Fungsi untuk membuat Struk (Header) dengan Data Lengkap
    public function create($data)
    {
        $userId = $_SESSION['user']['id'];

        // Check if this is a Midtrans transaction
        if (isset($data['midtrans_order_id'])) {
            $sql = "INSERT INTO transactions 
                    (user_id, total_amount, payment_method, pay_amount, change_amount, 
                     midtrans_order_id, midtrans_snap_token, midtrans_transaction_status) 
                    VALUES 
                    (:user_id, :total, :method, :pay, :change, :order_id, :snap_token, :status)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':total'   => $data['total_final'],
                ':method'  => 'MIDTRANS',
                ':pay'     => $data['total_final'],
                ':change'  => 0,
                ':order_id' => $data['midtrans_order_id'],
                ':snap_token' => $data['midtrans_snap_token'] ?? null,
                ':status' => 'pending'
            ]);
        } else {
            // Regular cash/QRIS/transfer transaction
            $sql = "INSERT INTO transactions 
                    (user_id, total_amount, payment_method, pay_amount, change_amount) 
                    VALUES 
                    (:user_id, :total, :method, :pay, :change)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':total'   => $data['total_final'],
                ':method'  => $data['payment_method'],
                ':pay'     => $data['pay_amount'],
                ':change'  => $data['change_amount']
            ]);
        }

        return $this->db->lastInsertId();
    }

    // Update Midtrans transaction status
    public function updateMidtransStatus($orderId, $status, $paymentType = null)
    {
        $sql = "UPDATE transactions 
                SET midtrans_transaction_status = :status,
                    midtrans_payment_type = :payment_type
                WHERE midtrans_order_id = :order_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':payment_type' => $paymentType,
            ':order_id' => $orderId
        ]);
    }

    // Get transaction by Midtrans order ID
    public function getByMidtransOrderId($orderId)
    {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE midtrans_order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetch();
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
