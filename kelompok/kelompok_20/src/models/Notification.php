<?php

declare(strict_types=1);

/**
 * Notification Model
 * Handles notification CRUD operations
 */
final class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Create a new notification
     * 
     * @param int $userId Target user ID
     * @param string $title Notification title
     * @param string $message Notification message
     * @param string|null $link Optional link to redirect
     * @return bool
     */
    public function create(int $userId, string $title, string $message, ?string $link = null): bool
    {
        $sql = "INSERT INTO notifications (user_id, title, message, link, is_read, created_at)
                VALUES (:user_id, :title, :message, :link, 0, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link
        ]);
    }

    /**
     * Get unread notifications for a user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of notifications to fetch
     * @return array
     */
    public function getUnread(int $userId, int $limit = 5): array
    {
        $sql = "SELECT id, title, message, link, is_read, created_at
                FROM notifications
                WHERE user_id = :user_id AND is_read = 0
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all notifications for a user
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of notifications to fetch
     * @return array
     */
    public function getAllByUserId(int $userId, int $limit = 20): array
    {
        $sql = "SELECT id, title, message, link, is_read, created_at
                FROM notifications
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a specific notification as read
     * 
     * @param int $id Notification ID
     * @param int $userId User ID (for ownership validation)
     * @return bool
     */
    public function markAsRead(int $id, int $userId): bool
    {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE id = :id AND user_id = :user_id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'      => $id,
            'user_id' => $userId
        ]);
    }

    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        $sql = "UPDATE notifications
                SET is_read = 1
                WHERE user_id = :user_id AND is_read = 0";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Count unread notifications for a user
     * 
     * @param int $userId User ID
     * @return int
     */
    public function countUnread(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM notifications
                WHERE user_id = :user_id AND is_read = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Delete old notifications (cleanup)
     * 
     * @param int $daysOld Delete notifications older than this
     * @return bool
     */
    public function deleteOld(int $daysOld = 30): bool
    {
        $sql = "DELETE FROM notifications
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
                AND is_read = 1";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['days' => $daysOld]);
    }
}
