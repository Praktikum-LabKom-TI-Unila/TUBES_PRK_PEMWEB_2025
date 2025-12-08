<?php

declare(strict_types=1);

/**
 * Claim Model
 * Handles claim operations for items (lost & found verification)
 */
final class Claim
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new claim
     * 
     * @param array $data Claim data (item_id, user_id, verification_answer)
     * @return int|false Returns claim ID on success, false on failure
     */
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

    /**
     * Get a single claim by ID
     * 
     * @param int $id Claim ID
     * @return array|false
     */
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

    /**
     * Get all claims for an item with user info
     * 
     * @param int $itemId Item ID
     * @return array
     */
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

    /**
     * Get all claims made by a user with item info
     * 
     * @param int $userId User ID
     * @return array
     */
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

    /**
     * Check if user has already claimed an item
     * 
     * @param int $itemId Item ID
     * @param int $userId User ID
     * @return bool
     */
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

    /**
     * Verify a claim (with transaction)
     * - Update this claim to 'verified'
     * - Reject all other claims for this item
     * - Close the item
     * 
     * @param int $claimId Claim ID to verify
     * @param int $itemId Item ID
     * @return bool
     */
    public function verifyClaim(int $claimId, int $itemId): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. Update this claim to 'verified'
            $sql1 = "UPDATE claims 
                     SET status = 'verified', updated_at = NOW()
                     WHERE id = :claim_id AND deleted_at IS NULL";
            $stmt1 = $this->db->prepare($sql1);
            $result1 = $stmt1->execute(['claim_id' => $claimId]);

            if (!$result1) {
                throw new Exception('Failed to verify claim');
            }

            // 2. Reject all other claims for this item
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

            // 3. Update item status to 'closed'
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

    /**
     * Reject a specific claim
     * 
     * @param int $claimId Claim ID
     * @param string|null $notes Admin notes
     * @return bool
     */
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

    /**
     * Cancel a pending claim (soft delete)
     * 
     * @param int $claimId Claim ID
     * @param int $userId User ID (for ownership validation)
     * @return bool
     */
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

    /**
     * Count pending claims for an item
     * 
     * @param int $itemId Item ID
     * @return int
     */
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

    /**
     * Check if user is the claimer
     * 
     * @param int $claimId Claim ID
     * @param int $userId User ID
     * @return bool
     */
    public function isOwner(int $claimId, int $userId): bool
    {
        $sql = "SELECT user_id FROM claims WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $claimId]);

        $claim = $stmt->fetch(PDO::FETCH_ASSOC);

        return $claim && (int) $claim['user_id'] === $userId;
    }

    /**
     * Get verified claim for an item
     * 
     * @param int $itemId Item ID
     * @return array|false
     */
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

    /**
     * Count verified claims (for admin dashboard)
     * 
     * @return int
     */
    public function countVerified(): int
    {
        $sql = "SELECT COUNT(*) FROM claims WHERE status = 'verified' AND deleted_at IS NULL";
        $stmt = $this->db->query($sql);

        return (int) $stmt->fetchColumn();
    }
}
