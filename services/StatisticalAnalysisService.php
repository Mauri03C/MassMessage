<?php
namespace App\Services;

class StatisticalAnalysisService {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function analyzeActivityPatterns($userId, $action, $days = 30) {
        $sql = "SELECT 
                    HOUR(created_at) as hour,
                    COUNT(*) as count,
                    AVG(COUNT(*)) OVER() as avg_count,
                    STDDEV(COUNT(*)) OVER() as std_dev
                FROM user_activities
                WHERE user_id = ? 
                    AND action = ? 
                    AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY HOUR(created_at)";
        
        return $this->db->query($sql, [$userId, $action, $days])->fetchAll();
    }
    
    public function getAnomalyThresholds($patterns) {
        $avgCount = $patterns[0]['avg_count'] ?? 0;
        $stdDev = $patterns[0]['std_dev'] ?? 0;
        
        return [
            'low' => max(1, $avgCount - $stdDev),
            'medium' => max(1, $avgCount),
            'high' => $avgCount + $stdDev
        ];
    }
    
    public function suggestAlertConditions($userId, $action) {
        $patterns = $this->analyzeActivityPatterns($userId, $action);
        $thresholds = $this->getAnomalyThresholds($patterns);
        
        $peakHours = array_filter($patterns, function($p) use ($thresholds) {
            return $p['count'] >= $thresholds['high'];
        });
        
        $quietHours = array_filter($patterns, function($p) use ($thresholds) {
            return $p['count'] <= $thresholds['low'];
        });
        
        return [
            'thresholds' => $thresholds,
            'peak_hours' => array_column($peakHours, 'hour'),
            'quiet_hours' => array_column($quietHours, 'hour')
        ];
    }
}