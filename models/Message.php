<?php
class Message {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function addMessage($data) {
        $this->db->query('INSERT INTO messages (user_id, type, subject, content, status, scheduled_at, created_at) 
                         VALUES (:user_id, :type, :subject, :content, :status, :scheduled_at, NOW())');
        
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':scheduled_at', $data['scheduled_at']);

        if($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function addRecipients($messageId, $recipients) {
        $success = true;
        foreach($recipients as $recipient) {
            $this->db->query('INSERT INTO message_recipients (message_id, recipient, status) 
                             VALUES (:message_id, :recipient, "pending")');
            
            $this->db->bind(':message_id', $messageId);
            $this->db->bind(':recipient', $recipient);

            if(!$this->db->execute()) {
                $success = false;
            }
        }
        return $success;
    }

    public function getMessagesByUser($userId, $limit = 10, $offset = 0) {
        $this->db->query('SELECT m.*, COUNT(mr.id) as recipient_count, 
                         SUM(CASE WHEN mr.status = "sent" THEN 1 ELSE 0 END) as sent_count,
                         SUM(CASE WHEN mr.status = "failed" THEN 1 ELSE 0 END) as failed_count
                         FROM messages m 
                         LEFT JOIN message_recipients mr ON m.id = mr.message_id
                         WHERE m.user_id = :user_id
                         GROUP BY m.id
                         ORDER BY m.created_at DESC
                         LIMIT :limit OFFSET :offset');

        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);

        return $this->db->resultSet();
    }

    public function getMessageById($id) {
        $this->db->query('SELECT * FROM messages WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getMessageRecipients($messageId) {
        $this->db->query('SELECT * FROM message_recipients WHERE message_id = :message_id');
        $this->db->bind(':message_id', $messageId);
        return $this->db->resultSet();
    }

    public function updateMessageStatus($id, $status) {
        $this->db->query('UPDATE messages SET status = :status WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function updateRecipientStatus($messageId, $recipient, $status) {
        $this->db->query('UPDATE message_recipients SET status = :status 
                         WHERE message_id = :message_id AND recipient = :recipient');
        $this->db->bind(':message_id', $messageId);
        $this->db->bind(':recipient', $recipient);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    public function getTotalMessages($userId) {
        $sql = "SELECT COUNT(*) as total FROM messages WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ)->total;
    }

    public function getMessagesByType($userId) {
        $sql = "SELECT type, COUNT(*) as count FROM messages WHERE user_id = ? GROUP BY type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDeliveryStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total_recipients,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
                FROM recipients r
                JOIN messages m ON r.message_id = m.id
                WHERE m.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getMessageStats($userId, $period, $type) {
        $periodClause = match($period) {
            'week' => 'AND m.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)',
            'month' => 'AND m.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)',
            'year' => 'AND m.created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)',
            default => ''
        };

        $typeClause = $type !== 'all' ? 'AND m.type = ?' : '';
        
        $sql = "SELECT 
                    DATE(m.created_at) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN r.status = 'delivered' THEN 1 ELSE 0 END) as delivered
                FROM messages m
                LEFT JOIN recipients r ON m.id = r.message_id
                WHERE m.user_id = ? $periodClause $typeClause
                GROUP BY DATE(m.created_at)
                ORDER BY date";

        $params = $type !== 'all' ? [$userId, $type] : [$userId];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getMessageReport($userId, $startDate, $endDate, $type) {
        $typeClause = $type !== 'all' ? 'AND m.type = ?' : '';
        
        $sql = "SELECT 
                    DATE(m.created_at) as date,
                    m.type,
                    m.subject,
                    COUNT(r.id) as recipients,
                    SUM(CASE WHEN r.status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN r.status = 'failed' THEN 1 ELSE 0 END) as failed
                FROM messages m
                LEFT JOIN recipients r ON m.id = r.message_id
                WHERE m.user_id = ?
                    AND DATE(m.created_at) BETWEEN ? AND ?
                    $typeClause
                GROUP BY m.id
                ORDER BY m.created_at DESC";

        $params = $type !== 'all' ? [$userId, $startDate, $endDate, $type] : [$userId, $startDate, $endDate];
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDeliveryTimeAnalysis($userId, $startDate, $endDate) {
        $sql = "SELECT 
                    AVG(TIMESTAMPDIFF(SECOND, m.created_at, r.updated_at)) as avg_delivery_time,
                    MIN(TIMESTAMPDIFF(SECOND, m.created_at, r.updated_at)) as min_delivery_time,
                    MAX(TIMESTAMPDIFF(SECOND, m.created_at, r.updated_at)) as max_delivery_time,
                    m.type
                FROM messages m
                JOIN recipients r ON m.id = r.message_id
                WHERE m.user_id = ?
                    AND r.status = 'delivered'
                    AND DATE(m.created_at) BETWEEN ? AND ?
                GROUP BY m.type";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPeakSendingTimes($userId) {
        $sql = "SELECT 
                    HOUR(created_at) as hour,
                    COUNT(*) as count,
                    type
                FROM messages
                WHERE user_id = ?
                    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY HOUR(created_at), type
                ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}