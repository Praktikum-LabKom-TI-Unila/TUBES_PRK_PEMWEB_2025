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

    
    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
        $stmt = $this->db->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Menyimpan token reset password ke tabel password_resets.
     * @param string $email Email pengguna.
     * @param string $token Token unik yang dibuat.
     * @param string $expiry Waktu kadaluarsa token.
     * @return bool True jika berhasil disimpan.
     */
    public function saveResetToken(string $email, string $token, string $expiry): bool
    {
        // Hapus token lama untuk email yang sama (opsional, tapi disarankan)
        $this->db->prepare("DELETE FROM password_resets WHERE email = :email")
                 ->execute([':email' => $email]);

        $sql = "INSERT INTO password_resets (email, token, created_at) VALUES (:email, :token, :created_at)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':created_at' => $expiry 
        ]);
    }

    /**
     * Mencari token reset password yang valid (belum kadaluarsa).
     * @param string $email Email pengguna.
     * @param string $token Token dari URL.
     * @return array|false Data token jika valid, false jika tidak ditemukan atau kadaluarsa.
     */
    public function findResetToken(string $email, string $token): array|false
    {
        $sql = "SELECT * FROM password_resets WHERE email = :email AND token = :token AND created_at > NOW() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':token' => $token
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Memperbarui password pengguna dan menghapus token reset yang sudah digunakan.
     * @param string $email Email pengguna.
     * @param string $hashedPassword Password baru yang sudah di-hash.
     * @param string $token Token reset yang harus dihapus.
     * @return bool True jika berhasil diperbarui.
     */
    public function updatePasswordAndDestroyToken(string $email, string $hashedPassword, string $token): bool
    {
        try {
            $this->db->beginTransaction();

            $sqlUser = "UPDATE users SET password = :password, updated_at = NOW() WHERE email = :email";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                ':password' => $hashedPassword,
                ':email' => $email
            ]);
            
            $sqlToken = "DELETE FROM password_resets WHERE email = :email AND token = :token";
            $stmtToken = $this->db->prepare($sqlToken);
            $stmtToken->execute([
                ':email' => $email,
                ':token' => $token
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
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
}
