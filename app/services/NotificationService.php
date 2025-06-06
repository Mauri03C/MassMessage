<?php
namespace App\Services;

use App\Models\NotificationTemplate;
use PHPMailer\PHPMailer\PHPMailer;
use PDO;

class NotificationService {
    private $db;
    private $templateModel;
    
    public function __construct(PDO $db) {
        $this->db = $db;
        $this->templateModel = new NotificationTemplate($db);
    }
    
    public function sendNotification($userId, $templateId, $variables = []) {
        // Obtener la plantilla
        $template = $this->templateModel->getById($templateId);
        if (!$template) {
            throw new \Exception('Plantilla no encontrada');
        }
        
        // Obtener información del usuario
        $sql = "SELECT email, notification_preferences FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new \Exception('Usuario no encontrado');
        }
        
        // Procesar la plantilla con las variables
        $subject = $this->processTemplate($template['subject'], $variables);
        $content = $this->processTemplate($template['content'], $variables);
        
        // Enviar notificación por email si está habilitado
        $preferences = json_decode($user['notification_preferences'], true);
        if ($preferences['email'] ?? true) {
            $this->sendEmail($user['email'], $subject, $content);
        }
        
        // Guardar notificación web
        $sql = "INSERT INTO notifications (user_id, title, content, created_at) 
                VALUES (:user_id, :title, :content, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $subject,
            ':content' => $content
        ]);
        
        return true;
    }
    
    private function processTemplate($template, $variables) {
        foreach ($variables as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    }
    
    private function sendEmail($to, $subject, $content) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['SMTP_PORT'];
            
            // Destinatarios
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($to);
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $content;
            
            $mail->send();
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error al enviar el email: ' . $mail->ErrorInfo);
        }
    }
}