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

    // --- START: FUNGSI RESET PASSWORD YANG STABIL ---

    public function saveResetToken(string $email, string $token): bool
    {
        try {
            // Hapus token lama untuk email yang sama
            $this->db->prepare("DELETE FROM password_resets WHERE email = :email")
                     ->execute([':email' => $email]);

            $sql = "INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, NOW())";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':email' => $email,
                ':token' => $token,
            ]);
        } catch (PDOException $e) {
            error_log('DB Error saveResetToken: ' . $e->getMessage());
            return false;
        }
    }

    public function findValidResetToken(string $email, string $token): array|false
    {
        try {
            // Cek apakah token cocok DAN dibuat kurang dari 1 jam yang lalu
            $sql = "SELECT * FROM password_resets 
                    WHERE email = :email 
                    AND token = :token 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR) 
                    LIMIT 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':token' => $token
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('DB Error findValidResetToken: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePassword(string $email, string $hashedPassword): bool
    {
        try {
            $sql = "UPDATE users SET password = :password, updated_at = NOW() WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':password' => $hashedPassword,
                ':email' => $email
            ]);
        } catch (PDOException $e) {
            error_log('DB Error updatePassword: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteResetToken(string $token): bool
    {
        try {
            $sql = "DELETE FROM password_resets WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':token' => $token]);
        } catch (PDOException $e) {
            error_log('DB Error deleteResetToken: ' . $e->getMessage());
            return false;
        }
    }

    // --- END: FUNGSI RESET PASSWORD YANG STABIL ---

    
    public function getAll(int $limit = 20, int $offset = 0): array
    {
        // Use SELECT * to avoid referencing column names that may differ between DBs.
        // Normalize the returned rows so the caller always has an `npm` field.
        $limit = max(0, (int) $limit);
        $offset = max(0, (int) $offset);

        $sql = "SELECT * FROM users WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT " . $limit . " OFFSET " . $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize: ensure 'npm' key exists (map from common column names)
        foreach ($rows as &$row) {
            if (!isset($row['npm'])) {
                if (isset($row['identity_number'])) {
                    $row['npm'] = $row['identity_number'];
                } elseif (isset($row['identitynumber'])) {
                    $row['npm'] = $row['identitynumber'];
                } else {
                    $row['npm'] = '';
                }
            }
        }

        return $rows;
    }

    
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
        $stmt = $this->db->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }

        if (isset($data['phone'])) {
            $fields[] = "phone = :phone";
            $params[':phone'] = $data['phone'];
        }

        if (!empty($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (isset($data['avatar'])) {
            $fields[] = "avatar = :avatar";
            $params[':avatar'] = $data['avatar'];
        }

        $fields[] = "updated_at = NOW()";

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    
    public function delete(int $id): bool
    {
        $sql = "UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    public function setActive(int $id, int $isActive): bool
    {
        $sql = "UPDATE users SET is_active = :is_active, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([':is_active' => $isActive, ':id' => $id]);
    }
}
