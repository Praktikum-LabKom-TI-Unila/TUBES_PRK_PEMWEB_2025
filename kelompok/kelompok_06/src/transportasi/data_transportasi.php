<?php
require_once '../config/database.php';

/**
 * Get all transportation data
 * @return array
 */
function getTransportasi()
{
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM transportasi ORDER BY id DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}
