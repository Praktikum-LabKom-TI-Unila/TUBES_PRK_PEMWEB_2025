<?php
declare(strict_types=1);

namespace Models;

use Database;
use PDO;

final class Product
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.id,
                    p.code,
                    p.name,
                    p.category_id,
                    c.name AS category_name,
                    c.code AS category_code,
                    p.unit,
                    p.current_stock,
                    p.min_stock,
                    p.current_price,
                    p.is_active,
                    p.created_at,
                    p.updated_at
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.id,
                    p.code,
                    p.name,
                    p.category_id,
                    c.name AS category_name,
                    c.code AS category_code,
                    p.unit,
                    p.current_stock,
                    p.min_stock,
                    p.current_price,
                    p.is_active,
                    p.created_at,
                    p.updated_at
             FROM products p
             JOIN product_categories c ON c.id = p.category_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $payload): array
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO products (code, name, category_id, unit, current_stock, min_stock, current_price, is_active)
             VALUES (:code, :name, :category_id, :unit, :current_stock, :min_stock, :current_price, :is_active)'
        );

        $stmt->execute([
            'code' => $payload['code'],
            'name' => $payload['name'],
            'category_id' => $payload['category_id'],
            'unit' => $payload['unit'],
            'current_stock' => $payload['current_stock'],
            'min_stock' => $payload['min_stock'],
            'current_price' => $payload['current_price'],
            'is_active' => $payload['is_active'],
        ]);

        return $this->find((int) $this->pdo->lastInsertId());
    }

    public function update(int $id, array $payload): ?array
    {
        $stmt = $this->pdo->prepare(
            'UPDATE products
             SET code = :code,
                 name = :name,
                 category_id = :category_id,
                 unit = :unit,
                 current_stock = :current_stock,
                 min_stock = :min_stock,
                 current_price = :current_price,
                 is_active = :is_active,
                 updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );

        $stmt->execute([
            'code' => $payload['code'],
            'name' => $payload['name'],
            'category_id' => $payload['category_id'],
            'unit' => $payload['unit'],
            'current_stock' => $payload['current_stock'],
            'min_stock' => $payload['min_stock'],
            'current_price' => $payload['current_price'],
            'is_active' => $payload['is_active'],
            'id' => $id,
        ]);

        return $this->find($id);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function syncStock(int $productId): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT COALESCE(SUM(quantity_remaining), 0) AS total_stock
             FROM product_batches
             WHERE product_id = :product_id AND is_active = 1'
        );
        $stmt->execute(['product_id' => $productId]);
        $total = (float) $stmt->fetchColumn();

        $update = $this->pdo->prepare(
            'UPDATE products SET current_stock = :stock, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $update->execute([
            'stock' => $total,
            'id' => $productId,
        ]);
    }
}
