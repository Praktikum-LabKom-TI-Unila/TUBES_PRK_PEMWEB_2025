<?php

declare(strict_types=1);

function setupPasswordResetsTable(): void
{
    $db = Database::getConnection();
    $sql = "
    CREATE TABLE IF NOT EXISTS password_resets (
        email VARCHAR(100) NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        created_at DATETIME NOT NULL,
        PRIMARY KEY (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    try {
        $db->exec($sql);
    } catch (PDOException $e) {
        error_log("Database Setup Error (password_resets): " . $e->getMessage());
    }
}