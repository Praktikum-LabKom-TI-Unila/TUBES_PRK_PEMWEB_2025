<?php

declare(strict_types=1);


final class Claim
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO claims (item_id, user_id, status, verification_answer, created_at)
                VALUES (:item_id, :user_id, 'pending', :verification_answer, NOW())";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            'item_id'             => $data['item_id'],
            'user_id'             => $data['user_id'],
            'verification_answer' => $data['verification_answer'] ?? null
        ]);

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    
    public function getById(int $id): array|false
    {
        $sql = "SELECT 
                    c.*,
                    u.name AS user_name,
                    u.email AS user_email,
                    u.phone AS user_phone,
                    u.avatar AS user_avatar,
                    i.title AS item_title,
                    i.user_id AS item_owner_id
                FROM claims c
                JOIN users u ON c.user_id = u.id
                JOIN items i ON c.item_id = i.id
                WHERE c.id = :id AND c.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function getByItemId(int $itemId): array
    {
        $sql = "SELECT 
                    c.id,
                    c.item_id,
                    c.user_id,
                    c.status,
                    c.verification_answer,
                    c.admin_notes,
                    c.created_at,
                    c.updated_at,
                    u.name AS user_name,
                    u.email AS user_email,
                    u.phone AS user_phone,
                    u.avatar AS user_avatar,
                    u.npm AS user_npm
                FROM claims c
                JOIN users u ON c.user_id = u.id
                WHERE c.item_id = :item_id AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT 
                    c.id,
                    c.item_id,
                    c.status,
                    c.verification_answer,
                    c.admin_notes,
                    c.created_at,
                    c.updated_at,
                    i.title AS item_title,
                    i.type AS item_type,
                    i.image_path AS item_image,
                    i.status AS item_status,
                    i.user_id AS item_owner_id,
                    owner.name AS item_owner_name
                FROM claims c
                JOIN items i ON c.item_id = i.id
                JOIN users owner ON i.user_id = owner.id
                WHERE c.user_id = :user_id AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function hasClaimed(int $itemId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM claims
                WHERE item_id = :item_id 
                AND user_id = :user_id 
                AND deleted_at IS NULL
                AND status != 'rejected'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'item_id' => $itemId,
            'user_id' => $userId
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    
    public function verifyClaim(int $claimId, int $itemId): bool
    {
        try {
            $this->db->beginTransaction();

            $sql1 = "UPDATE claims 
                     SET status = 'verified', updated_at = NOW()
                     WHERE id = :claim_id AND deleted_at IS NULL";
            $stmt1 = $this->db->prepare($sql1);
            $result1 = $stmt1->execute(['claim_id' => $claimId]);

            if (!$result1) {
                throw new Exception('Failed to verify claim');
            }

            $sql2 = "UPDATE claims 
                     SET status = 'rejected', updated_at = NOW()
                     WHERE item_id = :item_id 
                     AND id != :claim_id 
                     AND deleted_at IS NULL
                     AND status = 'pending'";
            $stmt2 = $this->db->prepare($sql2);
            $result2 = $stmt2->execute([
                'item_id'  => $itemId,
                'claim_id' => $claimId
            ]);

            if (!$result2) {
                throw new Exception('Failed to reject other claims');
            }

            $sql3 = "UPDATE items 
                     SET status = 'closed', updated_at = NOW()
                     WHERE id = :item_id AND deleted_at IS NULL";
            $stmt3 = $this->db->prepare($sql3);
            $result3 = $stmt3->execute(['item_id' => $itemId]);

            if (!$result3) {
                throw new Exception('Failed to close item');
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Claim verification failed: ' . $e->getMessage());
            return false;
        }
    }

    
    public function rejectClaim(int $claimId, ?string $notes = null): bool
    {
        $sql = "UPDATE claims 
                SET status = 'rejected', admin_notes = :notes, updated_at = NOW()
                WHERE id = :claim_id AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'claim_id' => $claimId,
            'notes'    => $notes
        ]);
    }

    
    public function cancel(int $claimId, int $userId): bool
    {
        $sql = "UPDATE claims 
                SET deleted_at = NOW()
                WHERE id = :claim_id 
                AND user_id = :user_id 
                AND status = 'pending'
                AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'claim_id' => $claimId,
            'user_id'  => $userId
        ]);
    }

    
    public function countPendingByItemId(int $itemId): int
    {
        $sql = "SELECT COUNT(*) FROM claims
                WHERE item_id = :item_id 
                AND status = 'pending'
                AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return (int) $stmt->fetchColumn();
    }

    
    public function isOwner(int $claimId, int $userId): bool
    {
        $sql = "SELECT user_id FROM claims WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $claimId]);

        $claim = $stmt->fetch(PDO::FETCH_ASSOC);

        return $claim && (int) $claim['user_id'] === $userId;
    }

    
    public function getVerifiedByItemId(int $itemId): array|false
    {
        $sql = "SELECT 
                    c.*,
                    u.name AS user_name,
                    u.email AS user_email,
                    u.phone AS user_phone,
                    u.avatar AS user_avatar
                FROM claims c
                JOIN users u ON c.user_id = u.id
                WHERE c.item_id = :item_id 
                AND c.status = 'verified'
                AND c.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function countVerified(): int
    {
        $sql = "SELECT COUNT(*) FROM claims WHERE status = 'verified' AND deleted_at IS NULL";
        $stmt = $this->db->query($sql);

        return (int) $stmt->fetchColumn();
    }

    
    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM claims WHERE user_id = :user_id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }
}
