<?php

declare(strict_types=1);

final class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function register(array $data): bool
    {
        $sql = "INSERT INTO users (name, identity_number, email, password, phone, role, is_active, created_at) 
                VALUES (:name, :identity_number, :email, :password, :phone, :role, :is_active, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name'            => $data['name'],
            ':identity_number' => $data['identity_number'],
            ':email'           => $data['email'],
            ':password'        => $data['password'],
            ':phone'           => $data['phone'] ?? null,
            ':role'            => $data['role'] ?? 'user',
            ':is_active'       => $data['is_active'] ?? 1
        ]);
    }

    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        return $stmt->fetch();
    }

    public function findByIdentityNumber(string $identityNumber): array|false
    {
        $sql = "SELECT * FROM users WHERE identity_number = :identity_number LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':identity_number' => $identityNumber]);

        return $stmt->fetch();
    }

    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE users SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':id' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== false;
    }

    public function identityNumberExists(string $identityNumber): bool
    {
        return $this->findByIdentityNumber($identityNumber) !== false;
    }

    /**
     * Get all users with pagination
     * 
     * @param int $limit Number of users per page
     * @param int $offset Offset for pagination
     * @return array
     */
    public function getAll(int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT 
                    id, name, npm, email, phone, avatar, role, is_active, 
                    created_at, updated_at, deleted_at
                FROM users
                WHERE deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all active users
     * 
     * @return int
     */
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
        $stmt = $this->db->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Soft delete a user
     * 
     * @param int $id User ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}
