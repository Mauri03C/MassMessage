<?php
class Widget {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getUserWidgets($userId) {
        $sql = "SELECT * FROM user_widgets WHERE user_id = ? ORDER BY position";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function saveWidget($userId, $type, $title, $config, $position) {
        $sql = "INSERT INTO user_widgets (user_id, widget_type, title, config, position)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $type, $title, json_encode($config), $position]);
    }

    public function updateWidgetPosition($widgetId, $position) {
        $sql = "UPDATE user_widgets SET position = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$position, $widgetId]);
    }

    public function deleteWidget($widgetId, $userId) {
        $sql = "DELETE FROM user_widgets WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$widgetId, $userId]);
    }

    public function updateWidgetConfig($widgetId, $userId, $config) {
        $sql = "UPDATE user_widgets SET config = ? WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([json_encode($config), $widgetId, $userId]);
    }
}