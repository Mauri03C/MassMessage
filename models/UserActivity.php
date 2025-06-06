<?php
namespace App\Models;

class UserActivity {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function logActivity($userId, $action, $details = null) {
        $sql = "INSERT INTO user_activities (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())";
        $result = $this->db->query($sql, [$userId, $action, $details]);
        
        if ($result) {
            $this->checkAlerts($userId, [
                'action' => $action,
                'details' => $details,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $this->notifyRealTime([
                'type' => 'activity',
                'data' => [
                    'user_id' => $userId,
                    'action' => $action,
                    'details' => $details,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]);
        }
        
        return $result;
    }
    
    private function checkAlerts($userId, $activity) {
        $alertModel = new Alert();
        $notificationService = new NotificationService();
        $alerts = $alertModel->getUserAlerts($userId);
        
        foreach ($alerts as $alert) {
            if ($alertModel->checkConditions($alert, $activity)) {
                switch ($alert['notification_method']) {
                    case 'email':
                        $notificationService->sendEmailNotification($this->getUser($userId), $alert, $activity);
                        break;
                    case 'web':
                        $notificationService->sendWebNotification($userId, $alert, $activity);
                        break;
                }
            }
        }
    }
    
    private function getUser($userId) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->query($sql, [$userId])->fetch();
    }
    
    private function notifyRealTime($data) {
        $client = new WebSocket\Client("ws://localhost:8080");
        $client->send(json_encode($data));
        $client->close();
    }

    public function getAnalytics($dateFrom = null, $dateTo = null) {
        $params = [];
        $where = [];
        
        if ($dateFrom) {
            $where[] = "created_at >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = "created_at <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // User engagement metrics
        $sql = "SELECT 
                    COUNT(DISTINCT user_id) as active_users,
                    COUNT(*) as total_activities,
                    COUNT(*) / COUNT(DISTINCT user_id) as avg_activities_per_user,
                    COUNT(CASE WHEN action = 'message_sent' THEN 1 END) as messages_sent,
                    COUNT(CASE WHEN action = 'template_created' THEN 1 END) as templates_created,
                    COUNT(CASE WHEN action = 'contact_imported' THEN 1 END) as contacts_imported,
                    HOUR(created_at) as hour,
                    COUNT(*) as hourly_count
                FROM user_activities
                $whereClause
                GROUP BY HOUR(created_at)";
        
        $metrics = $this->db->query($sql, $params)->fetchAll();
        
        // Peak activity times
        $sql = "SELECT 
                    DATE_FORMAT(created_at, '%H:00') as time_slot,
                    COUNT(*) as activity_count
                FROM user_activities
                $whereClause
                GROUP BY DATE_FORMAT(created_at, '%H:00')
                ORDER BY activity_count DESC
                LIMIT 5";
        
        $peakTimes = $this->db->query($sql, $params)->fetchAll();
        
        // User retention
        $sql = "SELECT 
                    u.id,
                    MIN(ua.created_at) as first_activity,
                    MAX(ua.created_at) as last_activity,
                    COUNT(*) as total_activities,
                    DATEDIFF(MAX(ua.created_at), MIN(ua.created_at)) as days_active
                FROM users u
                JOIN user_activities ua ON u.id = ua.user_id
                $whereClause
                GROUP BY u.id";
        
        $retention = $this->db->query($sql, $params)->fetchAll();
        
        return [
            'metrics' => $metrics,
            'peak_times' => $peakTimes,
            'retention' => $retention
        ];
    }
    
    public function getActivities($filters = [], $limit = 50, $offset = 0) {
        $where = [];
        $params = [];
        
        if (!empty($filters['user_id'])) {
            $where[] = "user_id = ?";
            $params[] = $filters['user_id'];
        }
        if (!empty($filters['action'])) {
            $where[] = "action = ?";
            $params[] = $filters['action'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "created_at >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "created_at <= ?";
            $params[] = $filters['date_to'];
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        $sql = "SELECT ua.*, u.username FROM user_activities ua 
                LEFT JOIN users u ON ua.user_id = u.id 
                $whereClause 
                ORDER BY ua.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    public function getActivityStats($dateFrom = null, $dateTo = null) {
        $params = [];
        $where = [];
        
        if ($dateFrom) {
            $where[] = "created_at >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = "created_at <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        $sql = "SELECT action, COUNT(*) as count, 
                DATE_FORMAT(created_at, '%Y-%m-%d') as date 
                FROM user_activities 
                $whereClause 
                GROUP BY action, DATE_FORMAT(created_at, '%Y-%m-%d') 
                ORDER BY date DESC, action";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
}