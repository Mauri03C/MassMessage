<?php
namespace App\Models;

use PDO;

class NotificationTemplate {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }
    
    public function create($data) {
        $sql = "INSERT INTO notification_templates (name, subject, content, variables, created_at) 
                VALUES (:name, :subject, :content, :variables, NOW())";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':subject' => $data['subject'],
            ':content' => $data['content'],
            ':variables' => json_encode($data['variables'])
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE notification_templates 
                SET name = :name, subject = :subject, content = :content, 
                    variables = :variables, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':subject' => $data['subject'],
            ':content' => $data['content'],
            ':variables' => json_encode($data['variables'])
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM notification_templates WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM notification_templates WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM notification_templates ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}