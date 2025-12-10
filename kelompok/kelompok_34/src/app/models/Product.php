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

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, price, stock, image) 
                VALUES (:name, :price, :stock, :image)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'image' => $data['image']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET name = :name, price = :price, stock = :stock, image = :image 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'price' => $data['price'],
            'stock' => $data['stock'],
            'image' => $data['image']
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function reduceStock($id, $qty)
    {
        $sql = "UPDATE {$this->table} SET stock = stock - :qty WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['qty' => $qty, 'id' => $id]);
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE :keyword ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }
}