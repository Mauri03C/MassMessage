<?php
namespace App\Models;

class Alert {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createAlert($userId, $type, $conditions, $notificationMethod) {
        $sql = "INSERT INTO alerts (user_id, type, conditions, notification_method, created_at) 
                VALUES (?, ?, ?, ?, NOW())";
        return $this->db->query($sql, [$userId, $type, json_encode($conditions), $notificationMethod]);
    }
    
    public function getUserAlerts($userId) {
        $sql = "SELECT * FROM alerts WHERE user_id = ? ORDER BY created_at DESC";
        return $this->db->query($sql, [$userId])->fetchAll();
    }
    
    public function checkConditions($alert, $activity) {
        $conditions = json_decode($alert['conditions'], true);
        $match = true;
        
        foreach ($conditions as $key => $value) {
            switch ($key) {
                case 'action':
                    $match = $match && $activity['action'] === $value;
                    break;
                case 'frequency':
                    $count = $this->getActivityCount($activity['user_id'], $activity['action']);
                    $match = $match && $count >= $value;
                    break;
                case 'time_window':
                    $match = $match && $this->isWithinTimeWindow($value);
                    break;
            }
        }
        
        return $match;
    }
    
    private function getActivityCount($userId, $action, $minutes = 60) {
        $sql = "SELECT COUNT(*) as count FROM user_activities 
                WHERE user_id = ? AND action = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)";
        return $this->db->query($sql, [$userId, $action, $minutes])->fetch()['count'];
    }
    
    private function isWithinTimeWindow($window) {
        $currentHour = (int)date('H');
        return $currentHour >= $window['start'] && $currentHour <= $window['end'];
    }

    public function suggestAlerts($userId) {
        $analysisService = new StatisticalAnalysisService();
        $suggestions = [];
        
        $actions = ['message_sent', 'login', 'template_created', 'contact_imported'];
        foreach ($actions as $action) {
            $analysis = $analysisService->suggestAlertConditions($userId, $action);
            
            $suggestions[$action] = [
                'normal_frequency' => round($analysis['thresholds']['medium']),
                'high_frequency' => round($analysis['thresholds']['high']),
                'recommended_window' => [
                    'start' => min($analysis['peak_hours']),
                    'end' => max($analysis['peak_hours'])
                ],
                'quiet_period' => [
                    'start' => min($analysis['quiet_hours']),
                    'end' => max($analysis['quiet_hours'])
                ]
            ];
        }
        
        return $suggestions;
    }
}