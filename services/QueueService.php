<?php
class QueueService {
    private $db;
    private $emailService;
    private $whatsAppService;
    private $smsService;

    public function __construct() {
        $this->db = new Database;
        $this->emailService = new EmailService;
        $this->whatsAppService = new WhatsAppService;
        $this->smsService = new SMSService;
    }

    public function processMessage($messageId) {
        $this->db->query('SELECT * FROM messages WHERE id = :id');
        $this->db->bind(':id', $messageId);
        $message = $this->db->single();

        if (!$message) return false;

        $this->db->query('SELECT * FROM recipients WHERE message_id = :message_id AND status = "pendiente"');
        $this->db->bind(':message_id', $messageId);
        $recipients = $this->db->resultSet();

        foreach ($recipients as $recipient) {
            try {
                $result = $this->sendMessage($message, $recipient);
                $this->updateRecipientStatus(
                    $recipient->id, 
                    $result['success'] ? 'enviado' : 'fallido',
                    $result['message'] ?? null
                );
            } catch (Exception $e) {
                $this->updateRecipientStatus($recipient->id, 'fallido', $e->getMessage());
            }
        }

        $this->updateMessageStatus($messageId);
        return true;
    }

    private function sendMessage($message, $recipient) {
        switch ($message->type) {
            case 'email':
                return $this->emailService->send($recipient->contact, $message->subject, $message->content);
            case 'whatsapp':
                return $this->whatsAppService->send($recipient->contact, $message->content);
            case 'sms':
                return $this->smsService->send($recipient->contact, $message->content);
            default:
                throw new Exception('Tipo de mensaje no soportado');
        }
    }

    private function updateRecipientStatus($recipientId, $status, $errorMessage = null) {
        $this->db->query(
            'UPDATE recipients SET status = :status, error_message = :error_message, sent_at = :sent_at 
            WHERE id = :id'
        );
        $this->db->bind(':id', $recipientId);
        $this->db->bind(':status', $status);
        $this->db->bind(':error_message', $errorMessage);
        $this->db->bind(':sent_at', date('Y-m-d H:i:s'));
        return $this->db->execute();
    }

    private function updateMessageStatus($messageId) {
        // Actualizar contadores
        $this->db->query(
            'UPDATE messages m 
            SET sent_count = (SELECT COUNT(*) FROM recipients WHERE message_id = m.id AND status = "enviado"),
                failed_count = (SELECT COUNT(*) FROM recipients WHERE message_id = m.id AND status = "fallido"),
                status = CASE 
                    WHEN (SELECT COUNT(*) FROM recipients WHERE message_id = m.id AND status = "pendiente") = 0 THEN 
                        CASE 
                            WHEN (SELECT COUNT(*) FROM recipients WHERE message_id = m.id AND status = "fallido") = 0 THEN "enviado"
                            ELSE "fallido"
                        END
                    ELSE "pendiente"
                END
            WHERE id = :id'
        );
        $this->db->bind(':id', $messageId);
        return $this->db->execute();
    }
}