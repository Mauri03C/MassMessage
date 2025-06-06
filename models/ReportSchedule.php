<?php
class ReportSchedule {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($userId, $name, $frequency, $recipients, $reportType, $format = 'pdf') {
        $sql = "INSERT INTO report_schedules (user_id, name, frequency, recipients, report_type, format, next_run)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $nextRun = $this->calculateNextRun($frequency);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $name,
            $frequency,
            $recipients,
            $reportType,
            $format,
            $nextRun
        ]);
    }

    public function getSchedules($userId) {
        $sql = "SELECT * FROM report_schedules WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateNextRun($scheduleId) {
        $sql = "UPDATE report_schedules 
                SET next_run = ?, last_run = NOW() 
                WHERE id = ?";

        $schedule = $this->getScheduleById($scheduleId);
        $nextRun = $this->calculateNextRun($schedule->frequency);

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nextRun, $scheduleId]);
    }

    private function calculateNextRun($frequency) {
        $now = new DateTime();
        switch ($frequency) {
            case 'daily':
                return $now->modify('+1 day')->format('Y-m-d H:i:s');
            case 'weekly':
                return $now->modify('+1 week')->format('Y-m-d H:i:s');
            case 'monthly':
                return $now->modify('+1 month')->format('Y-m-d H:i:s');
            default:
                return $now->format('Y-m-d H:i:s');
        }
    }

    public function getScheduleById($id) {
        $sql = "SELECT * FROM report_schedules WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getDueSchedules() {
        $sql = "SELECT * FROM report_schedules WHERE next_run <= NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}