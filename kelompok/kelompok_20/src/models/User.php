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
}
