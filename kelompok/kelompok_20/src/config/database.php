<?php

declare(strict_types=1);

final class Database
{
    private const DB_HOST = 'localhost';
    private const DB_NAME = 'myunila_lostfound';
    private const DB_USER = 'root';
    private const DB_PASS = '';
    private const DB_CHARSET = 'utf8mb4';
    
    private static ?PDO $connection = null;
    
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;dbname=%s;charset=%s',
                    self::DB_HOST,
                    self::DB_NAME,
                    self::DB_CHARSET
                );
                
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                
                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                die('Koneksi database gagal. Silakan hubungi administrator.');
            }
        }
        
        return self::$connection;
    }
    
    private function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }
}
