<?php

class Database
{
  private $conn;

  public function __construct()
  {
    $host = '127.0.0.1';
    $name = 'lokapos';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
      $this->conn = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
      die('DB Connection failed: ' . $e->getMessage());
    }
  }

  public function getConnection()
  {
    return $this->conn;
  }
}
