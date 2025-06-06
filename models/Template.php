<?php
class Template {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getTemplatesByUser($userId) {
        $this->db->query('SELECT * FROM templates WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function addTemplate($data) {
        $this->db->query('INSERT INTO templates (user_id, name, type, subject, content) VALUES (:user_id, :name, :type, :subject, :content)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':content', $data['content']);
        return $this->db->execute();
    }

    public function getTemplateById($id) {
        $this->db->query('SELECT * FROM templates WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateTemplate($data) {
        $this->db->query('UPDATE templates SET name = :name, type = :type, subject = :subject, content = :content WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':content', $data['content']);
        return $this->db->execute();
    }

    public function deleteTemplate($id, $userId) {
        $this->db->query('DELETE FROM templates WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $userId);
        return $this->db->execute();
    }
}