<?php

declare(strict_types=1);

/**
 * Comment Model
 * Handles comment CRUD operations for items
 */
final class Comment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new comment
     * 
     * @param array $data Comment data (item_id, user_id, body)
     * @return int|false Returns comment ID on success, false on failure
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO comments (item_id, user_id, body, created_at)
                VALUES (:item_id, :user_id, :body, NOW())";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id'],
            'body'    => $data['body']
        ]);

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Get all comments for an item with user info
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
                    c.body,
                    c.created_at,
                    u.name AS user_name,
                    u.avatar AS user_avatar,
                    u.role AS user_role
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.item_id = :item_id AND c.deleted_at IS NULL
                ORDER BY c.created_at ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single comment by ID
     * 
     * @param int $id Comment ID
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        $sql = "SELECT 
                    c.id,
                    c.item_id,
                    c.user_id,
                    c.body,
                    c.created_at,
                    u.name AS user_name,
                    u.avatar AS user_avatar,
                    u.role AS user_role
                FROM comments c
                JOIN users u ON c.user_id = u.id
                WHERE c.id = :id AND c.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Soft delete a comment
     * 
     * @param int $id Comment ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "UPDATE comments SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Count comments for an item
     * 
     * @param int $itemId Item ID
     * @return int
     */
    public function countByItemId(int $itemId): int
    {
        $sql = "SELECT COUNT(*) FROM comments
                WHERE item_id = :item_id AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['item_id' => $itemId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get comments by user ID
     * 
     * @param int $userId User ID
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $sql = "SELECT 
                    c.id,
                    c.item_id,
                    c.body,
                    c.created_at,
                    i.title AS item_title,
                    i.type AS item_type
                FROM comments c
                JOIN items i ON c.item_id = i.id
                WHERE c.user_id = :user_id AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if user is the owner of a comment
     * 
     * @param int $commentId Comment ID
     * @param int $userId User ID
     * @return bool
     */
    public function isOwner(int $commentId, int $userId): bool
    {
        $sql = "SELECT user_id FROM comments WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $commentId]);

        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $comment && (int) $comment['user_id'] === $userId;
    }
}
