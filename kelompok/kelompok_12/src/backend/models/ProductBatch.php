<?php
declare(strict_types=1);

namespace Models;

use Database;
use PDO;

final class ProductBatch
{
    private PDO $pdo;

    public function __construct(private readonly Product $productModel = new Product())
    {
        $this->pdo = Database::getConnection();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT b.id,
                    b.product_id,
                    p.name AS product_name,
                    p.code AS product_code,
                    b.batch_code,
                    b.purchase_date,
                    b.purchase_price,
                    b.quantity,
                    b.quantity_used,
                    b.quantity_remaining,
                    b.selling_price,
                    b.is_active,
                    b.created_at
             FROM product_batches b
             JOIN products p ON p.id = b.product_id
             ORDER BY b.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT b.id,
                    b.product_id,
                    p.name AS product_name,
                    p.code AS product_code,
                    b.batch_code,
                    b.purchase_date,
                    b.purchase_price,
                    b.quantity,
                    b.quantity_used,
                    b.quantity_remaining,
                    b.selling_price,
                    b.is_active,
                    b.created_at
             FROM product_batches b
             JOIN products p ON p.id = b.product_id
             WHERE b.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $payload): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO product_batches (product_id, batch_code, purchase_date, purchase_price, quantity, quantity_used, quantity_remaining, selling_price, is_active)
             VALUES (:product_id, :batch_code, :purchase_date, :purchase_price, :quantity, :quantity_used, :quantity_remaining, :selling_price, :is_active)'
        );

        $stmt->execute([
            'product_id' => $payload['product_id'],
            'batch_code' => $payload['batch_code'],
            'purchase_date' => $payload['purchase_date'],
            'purchase_price' => $payload['purchase_price'],
            'quantity' => $payload['quantity'],
            'quantity_used' => $payload['quantity_used'],
            'quantity_remaining' => $payload['quantity_remaining'],
            'selling_price' => $payload['selling_price'],
            'is_active' => $payload['is_active'],
        ]);

        $id = (int) $this->pdo->lastInsertId();
        $this->productModel->syncStock($payload['product_id']);

        return $this->find($id);
    }

    public function update(int $id, array $payload, ?int $previousProductId = null): ?array
    {
        $stmt = $this->pdo->prepare(
            'UPDATE product_batches
             SET product_id = :product_id,
                 batch_code = :batch_code,
                 purchase_date = :purchase_date,
                 purchase_price = :purchase_price,
                 quantity = :quantity,
                 quantity_used = :quantity_used,
                 quantity_remaining = :quantity_remaining,
                 selling_price = :selling_price,
                 is_active = :is_active
             WHERE id = :id'
        );

        $stmt->execute([
            'product_id' => $payload['product_id'],
            'batch_code' => $payload['batch_code'],
            'purchase_date' => $payload['purchase_date'],
            'purchase_price' => $payload['purchase_price'],
            'quantity' => $payload['quantity'],
            'quantity_used' => $payload['quantity_used'],
            'quantity_remaining' => $payload['quantity_remaining'],
            'selling_price' => $payload['selling_price'],
            'is_active' => $payload['is_active'],
            'id' => $id,
        ]);

        $this->productModel->syncStock($payload['product_id']);
        if ($previousProductId !== null && $previousProductId !== $payload['product_id']) {
            $this->productModel->syncStock($previousProductId);
        }

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $batch = $this->find($id);
        $stmt = $this->pdo->prepare('DELETE FROM product_batches WHERE id = :id');
        $deleted = $stmt->execute(['id' => $id]);

        if ($deleted && $batch !== null) {
            $this->productModel->syncStock((int) $batch['product_id']);
        }

        return $deleted;
    }
}
