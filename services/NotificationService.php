<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class NotificationService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        // Configuración del servidor SMTP
        $this->mailer->Host = SMTP_HOST;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = SMTP_USER;
        $this->mailer->Password = SMTP_PASS;
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = SMTP_PORT;
        $this->mailer->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    }
    
    public function sendEmailNotification($user, $alert, $activity, $templateId = null) {
        $this->mailer->addAddress($user['email']);
        
        if ($templateId) {
            $template = (new NotificationTemplate())->getTemplate($templateId);
            $this->mailer->Subject = $this->parseTemplate($template['subject'], $activity);
            $body = $this->parseTemplate($template['email_body'], $activity);
        } else {
            $this->mailer->Subject = "Alerta de Actividad - MassMessage";
            $body = $this->getDefaultTemplate($alert, $activity);
        }
        
        $this->mailer->Body = $body;
        $this->mailer->AltBody = strip_tags($body);
        
        return $this->mailer->send();
    }
    
    public function sendWebNotification($userId, $alert, $activity, $templateId = null) {
        if ($templateId) {
            $template = (new NotificationTemplate())->getTemplate($templateId);
            $message = $this->parseTemplate($template['web_body'], $activity);
        } else {
            $message = $this->getDefaultMessage($alert, $activity);
        }
        
        $notification = [
            'user_id' => $userId,
            'title' => 'Alerta de Actividad',
            'message' => $message,
            'type' => 'alert',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $sql = "INSERT INTO notifications (user_id, title, message, type, created_at) 
                VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($sql, array_values($notification));
    }
    
    private function parseTemplate($template, $activity) {
        $placeholders = [
            '{action}' => $activity['action'],
            '{username}' => $activity['username'],
            '{datetime}' => $activity['created_at'],
            '{details}' => $activity['details']
        ];
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }
    
    private function getNotificationMessage($alert, $activity) {
        return "Actividad detectada: {$activity['action']} por {$activity['username']}";
    }
}
class NotificationService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createNotification($userId, $type, $message, $data = null) {
        $sql = "INSERT INTO notifications (user_id, type, message, data)
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $userId,
            $type,
            $message,
            $data ? json_encode($data) : null
        ]);
    }

    public function getUserNotifications($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function markAsRead($notificationId, $userId) {
        $sql = "UPDATE notifications 
                SET read_at = NOW() 
                WHERE id = ? AND user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$notificationId, $userId]);
    }

    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = ? AND read_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_OBJ)->count;
    }
}