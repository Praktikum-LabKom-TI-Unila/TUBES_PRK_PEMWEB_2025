<?php

class User extends Model
{
  protected string $table = 'users';

  public function create($data)
  {
    $sql = "INSERT INTO {$this->table} (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
      'name'     => $data['name'],
      'email'    => $data['email'],
      'password' => $data['password'],
    ]);
  }

  public function findByEmail($email)
  {
    $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
  }
}
