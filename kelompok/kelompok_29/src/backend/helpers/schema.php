<?php
function table_has_column(PDO $pdo, string $table, string $column): bool
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
        return false;
    }

    static $cache = [];
    $cacheKey = $table . ':' . $column;
    if (array_key_exists($cacheKey, $cache)) {
        return $cache[$cacheKey];
    }

    $stmt = $pdo->prepare("SHOW COLUMNS FROM {$table} LIKE :column_name");
    $stmt->execute([':column_name' => $column]);
    $cache[$cacheKey] = (bool) $stmt->fetch();

    return $cache[$cacheKey];
}
