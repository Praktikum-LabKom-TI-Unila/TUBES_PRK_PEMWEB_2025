<?php

class Product extends Model
{
  protected string $table = 'products';

  public function getAll()
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY name ASC");
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function find($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
  }

  public function reduceStock($id, $qty)
  {
    $sql = "UPDATE {$this->table} SET stock = stock - :qty WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['qty' => $qty, 'id' => $id]);
  }
}