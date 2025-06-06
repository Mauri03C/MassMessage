<?php
namespace App\Models;

class NotificationTemplate {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createTemplate($userId, $name, $subject, $emailBody, $webBody) {
        $sql = "INSERT INTO notification_templates (user_id, name, subject, email_body, web_body, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        return $this->db->query($sql, [$userId, $name, $subject, $emailBody, $webBody]);
    }
    
    public function getUserTemplates($userId) {
        $sql = "SELECT * FROM notification_templates WHERE user_id = ? ORDER BY name";
        return $this->db->query($sql, [$userId])->fetchAll();
    }
    
    public function getTemplate($id) {
        $sql = "SELECT * FROM notification_templates WHERE id = ?";
        return $this->db->query($sql, [$id])->fetch();
    }
    
    public function updateTemplate($id, $name, $subject, $emailBody, $webBody) {
        $sql = "UPDATE notification_templates 
                SET name = ?, subject = ?, email_body = ?, web_body = ? 
                WHERE id = ?";
        return $this->db->query($sql, [$name, $subject, $emailBody, $webBody, $id]);
    }
    
    public function deleteTemplate($id) {
        $sql = "DELETE FROM notification_templates WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}