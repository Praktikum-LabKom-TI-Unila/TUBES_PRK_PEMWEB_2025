<?php

/**
 * Permission Model
 */

require_once ROOT_PATH . '/core/Model.php';

class Permission extends Model
{
    protected $table = 'permissions';

    /**
     * Get all permissions
     */
    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id ASC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get all active permissions
     */
    public function getActive()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY id ASC";
        $stmt = $this->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Find multiple permissions by IDs
     */
    public function findByIds($ids = [])
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM {$this->table} WHERE id IN ({$placeholders})";

        $stmt = $this->query($sql, $ids);
        return $stmt->fetchAll();
    }
}
