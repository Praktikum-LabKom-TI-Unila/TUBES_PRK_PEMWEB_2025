<?php

declare(strict_types=1);

final class Item
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int|false
    {
        $sql = "INSERT INTO items (
                    user_id, category_id, location_id, title, description, 
                    type, incident_date, image_path, status,
                    is_safe_claim, security_question, security_answer, created_at
                ) VALUES (
                    :user_id, :category_id, :location_id, :title, :description,
                    :type, :incident_date, :image_path, :status,
                    :is_safe_claim, :security_question, :security_answer, NOW()
                )";

        $stmt = $this->db->prepare($sql);

        // UPDATE: Default status diubah dari 'open' menjadi 'pending'
        // agar postingan baru masuk moderasi dulu.
        $result = $stmt->execute([
            'user_id'           => $data['user_id'],
            'category_id'       => $data['category_id'],
            'location_id'       => $data['location_id'],
            'title'             => $data['title'],
            'description'       => $data['description'],
            'type'              => $data['type'],
            'incident_date'     => $data['incident_date'],
            'image_path'        => $data['image_path'] ?? null,
            'status'            => $data['status'] ?? 'pending', 
            'is_safe_claim'     => $data['is_safe_claim'] ?? 0,
            'security_question' => $data['security_question'] ?? null,
            'security_answer'   => $data['security_answer'] ?? null
        ]);

        return $result ? (int) $this->db->lastInsertId() : false;
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE items SET 
                    category_id = :category_id,
                    location_id = :location_id,
                    title = :title,
                    description = :description,
                    type = :type,
                    incident_date = :incident_date,
                    image_path = :image_path,
                    is_safe_claim = :is_safe_claim,
                    security_question = :security_question,
                    security_answer = :security_answer,
                    updated_at = NOW()
                WHERE id = :id AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id'                => $id,
            'category_id'       => $data['category_id'],
            'location_id'       => $data['location_id'],
            'title'             => $data['title'],
            'description'       => $data['description'],
            'type'              => $data['type'],
            'incident_date'     => $data['incident_date'],
            'image_path'        => $data['image_path'],
            'is_safe_claim'     => $data['is_safe_claim'] ?? 0,
            'security_question' => $data['security_question'] ?? null,
            'security_answer'   => $data['security_answer'] ?? null
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = "UPDATE items SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE items SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id, 'status' => $status]);
    }
    
    private function buildFilterConditions(array $filters): array
    {
        $conditions = ["i.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['type'])) {
            $conditions[] = "i.type = :type";
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['category_id'])) {
            $conditions[] = "i.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['location_id'])) {
            $conditions[] = "i.location_id = :location_id";
            $params['location_id'] = $filters['location_id'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = "i.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $conditions[] = "(i.title LIKE :keyword OR i.description LIKE :keyword2)";
            $params['keyword'] = '%' . $filters['keyword'] . '%';
            $params['keyword2'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = "i.incident_date >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = "i.incident_date <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['user_id'])) {
            $conditions[] = "i.user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        return [
            'where' => implode(' AND ', $conditions),
            'params' => $params
        ];
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT 
                    i.*,
                    u.name AS user_name,
                    u.avatar AS user_avatar,
                    c.name AS category_name,
                    l.name AS location_name
                FROM items i
                JOIN users u ON i.user_id = u.id
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id";

        $filterData = $this->buildFilterConditions($filters);
        $sql .= " WHERE " . $filterData['where'];
        $params = $filterData['params'];

        $sort = $filters['sort'] ?? 'newest';
        $orderDirection = ($sort === 'oldest') ? 'ASC' : 'DESC';
        $sql .= " ORDER BY i.created_at " . $orderDirection;

        if (isset($filters['limit']) && $filters['limit'] > 0) {
            $sql .= " LIMIT " . (int) $filters['limit'];

            if (isset($filters['offset']) && $filters['offset'] > 0) {
                $sql .= " OFFSET " . (int) $filters['offset'];
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAllFiltered(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) 
                FROM items i
                JOIN users u ON i.user_id = u.id
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id";

        $filterData = $this->buildFilterConditions($filters);
        $sql .= " WHERE " . $filterData['where'];

        $stmt = $this->db->prepare($sql);
        $stmt->execute($filterData['params']);

        return (int) $stmt->fetchColumn();
    }

    public function getById(int $id): array|false
    {
        $sql = "SELECT 
                    i.*,
                    u.id AS user_id,
                    u.name AS user_name,
                    u.email AS user_email,
                    u.phone AS user_phone,
                    u.avatar AS user_avatar,
                    c.name AS category_name,
                    l.name AS location_name
                FROM items i
                JOIN users u ON i.user_id = u.id
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id
                WHERE i.id = :id AND i.deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT 
                    i.*,
                    c.name AS category_name,
                    l.name AS location_name,
                    (SELECT COUNT(*) FROM claims cl WHERE cl.item_id = i.id AND cl.deleted_at IS NULL) AS claims_count
                FROM items i
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id
                WHERE i.user_id = :user_id AND i.deleted_at IS NULL
                ORDER BY i.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories(): array
    {
        $sql = "SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY name ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLocations(): array
    {
        $sql = "SELECT * FROM locations WHERE deleted_at IS NULL ORDER BY name ASC";
        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByType(string $type): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE type = :type AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['type' => $type]);

        return (int) $stmt->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE status = :status AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['status' => $status]);

        return (int) $stmt->fetchColumn();
    }

    public function countByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE user_id = :user_id AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM items WHERE deleted_at IS NULL";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    public function getRecent(int $limit = 6): array
    {
        return $this->getAll(['limit' => $limit]);
    }

    public function findMatches(array $targetItem, int $limit = 5): array
    {
        $oppositeType = ($targetItem['type'] === 'lost') ? 'found' : 'lost';

        $sql = "SELECT 
                    i.*,
                    u.name AS user_name,
                    u.avatar AS user_avatar,
                    c.name AS category_name,
                    l.name AS location_name,
                    CASE WHEN i.location_id = :target_location_id THEN 1 ELSE 0 END AS location_match
                FROM items i
                JOIN users u ON i.user_id = u.id
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id
                WHERE i.deleted_at IS NULL
                  AND i.id != :target_id
                  AND i.type = :opposite_type
                  AND i.category_id = :category_id
                  AND i.status = 'open'
                ORDER BY location_match DESC, i.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':target_location_id', (int) $targetItem['location_id'], PDO::PARAM_INT);
        $stmt->bindValue(':target_id', (int) $targetItem['id'], PDO::PARAM_INT);
        $stmt->bindValue(':opposite_type', $oppositeType, PDO::PARAM_STR);
        $stmt->bindValue(':category_id', (int) $targetItem['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isOwner(int $itemId, int $userId): bool
    {
        $sql = "SELECT user_id FROM items WHERE id = :id AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $itemId]);

        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        return $item && (int) $item['user_id'] === $userId;
    }

    public function getStats(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_items,
                    SUM(CASE WHEN type = 'lost' THEN 1 ELSE 0 END) as total_lost,
                    SUM(CASE WHEN type = 'found' THEN 1 ELSE 0 END) as total_found,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as total_open,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as total_closed
                FROM items
                WHERE deleted_at IS NULL";

        $stmt = $this->db->query($sql);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_items' => 0,
            'total_lost' => 0,
            'total_found' => 0,
            'total_open' => 0,
            'total_closed' => 0
        ];
    }

    public function getPendingItems(): array
    {
        $sql = "SELECT 
                    i.*,
                    u.name AS user_name,
                    c.name AS category_name,
                    l.name AS location_name
                FROM items i
                JOIN users u ON i.user_id = u.id
                JOIN categories c ON i.category_id = c.id
                JOIN locations l ON i.location_id = l.id
                WHERE i.status = 'pending' AND i.deleted_at IS NULL
                ORDER BY i.created_at DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}