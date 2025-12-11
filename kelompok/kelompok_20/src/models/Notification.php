<?php

declare(strict_types=1);


final class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    
    public function create(int $userId, string $title, string $message, ?string $link = null, string $type = 'default'): bool
    {
        $sql = "INSERT INTO notifications (user_id, title, message, link, type, is_read, created_at)
                VALUES (:user_id, :title, :message, :link, :type, 0, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'type'    => $type
        ]);
    }

    
    public function getUnread(int $userId, int $limit = 5): array
    {
        try {
            $limit = (int) $limit;
            $sql = "SELECT id, title, message, link, type, is_read, created_at
                    FROM notifications
                    WHERE user_id = :user_id AND is_read = 0
                    ORDER BY created_at DESC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Notification::getUnread error: ' . $e->getMessage());
            return [];
        }
    }

    
    public function getAllByUserId(int $userId, int $limit = 20, ?string $typeFilter = null): array
    {
        try {
            $limit = (int) $limit;
            $sql = "SELECT id, title, message, link, type, is_read, created_at
                    FROM notifications
                    WHERE user_id = :user_id
                    ORDER BY created_at DESC
                    LIMIT " . $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Notification::getAllByUserId error: ' . $e->getMessage());
            return [];
        }
    }

    
    public function markAsRead(int $id, int $userId): bool
    {
        try {
            $sql = "UPDATE notifications
                    SET is_read = 1
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                'id'      => $id,
                'user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log('Notification::markAsRead error: ' . $e->getMessage());
            return false;
        }
    }

    
    public function markAllAsRead(int $userId): bool
    {
        try {
            $sql = "UPDATE notifications
                    SET is_read = 1
                    WHERE user_id = :user_id AND is_read = 0";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $e) {
            error_log('Notification::markAllAsRead error: ' . $e->getMessage());
            return false;
        }
    }

    
    public function countUnread(int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM notifications
                    WHERE user_id = :user_id AND is_read = 0";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Notification::countUnread error: ' . $e->getMessage());
            return 0;
        }
    }

    
    public function deleteOld(int $daysOld = 30): bool
    {
        $sql = "DELETE FROM notifications
                WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)
                AND is_read = 1";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['days' => $daysOld]);
    }

    public function countByType(int $userId, string $typeFilter): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id";
            
            if ($typeFilter === 'reports') {
                $sql .= " AND type IN ('item_created', 'item_comment', 'new_claim', 'item_match')";
            } elseif ($typeFilter === 'claims') {
                $sql .= " AND type IN ('claim_verified', 'claim_rejected', 'new_claim')";
            } elseif ($typeFilter === 'unread') {
                $sql .= " AND is_read = 0";
            }
            // 'all' tidak perlu WHERE tambahan, ambil semua notifikasi user

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);

            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Notification::countByType error: ' . $e->getMessage());
            return 0;
        }
    }
}
