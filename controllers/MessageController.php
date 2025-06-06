<?php
class MessageController extends Controller {
    private $messageModel;
    private $emailService;
    private $smsService;
    private $whatsappService;

    public function __construct() {
        if(!isLoggedIn()) {
            redirect('auth/login');
        }

        // Inicializar modelos y servicios con inyección de dependencias
        $this->messageModel = $this->model('Message');
        $this->emailService = new EmailService();
        $this->smsService = new SMSService();
        $this->whatsappService = new WhatsAppService();

        // Verificar token CSRF para todas las solicitudes POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrfToken();
        }
    }

    public function index() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = max(0, ($page - 1) * $limit);

            $messages = $this->messageModel->getMessagesByUser($_SESSION['user_id'], $limit, $offset);
            $totalMessages = $this->messageModel->countUserMessages($_SESSION['user_id']);
            $totalPages = ceil($totalMessages / $limit);

            $data = [
                'messages' => $messages,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'token' => $this->generateCsrfToken()
            ];

            $this->view('messages/index', $data);
        } catch (Exception $e) {
            $this->handleError($e, 'Error al cargar los mensajes');
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validar y limpiar entrada
                $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
                $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
                $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
                $recipients = json_decode($_POST['recipients'] ?? '[]', true);

                // Validar tipo de mensaje
                if (!in_array($type, ['email', 'sms', 'whatsapp'])) {
                    throw new Exception('Tipo de mensaje no válido');
                }

                // Validar destinatarios
                if (empty($recipients)) {
                    throw new Exception('Debe especificar al menos un destinatario');
                }


                // Crear mensaje en la base de datos
                $messageId = $this->messageModel->createMessage([
                    'user_id' => $_SESSION['user_id'],
                    'subject' => $subject,
                    'content' => $content,
                    'type' => $type,
                    'status' => 'pending',
                    'recipient_count' => count($recipients)
                ]);

                // Enviar mensajes según el tipo
                $successCount = 0;
                $failedRecipients = [];

                foreach ($recipients as $recipient) {
                    try {
                        switch ($type) {
                            case 'email':
                                $this->emailService->send($recipient['email'], $subject, $content);
                                break;
                            case 'sms':
                                $this->smsService->send($recipient['phone'], $content);
                                break;
                            case 'whatsapp':
                                $this->whatsappService->send($recipient['phone'], $content);
                                break;
                        }
                        $successCount++;
                    } catch (Exception $e) {
                        $failedRecipients[] = [
                            'recipient' => $recipient,
                            'error' => $e->getMessage()
                        ];
                    }
                }


                // Actualizar estado del mensaje
                $status = $successCount > 0 ? ($successCount === count($recipients) ? 'sent' : 'partial') : 'failed';
                $this->messageModel->updateMessageStatus($messageId, $status);

                // Redirigir con mensaje de éxito
                $_SESSION['success_message'] = "Mensaje enviado correctamente a $successCount de " . count($recipients) . " destinatarios";
                if (!empty($failedRecipients)) {
                    $_SESSION['warning_message'] = 'Algunos mensajes no pudieron ser enviados';
                }

                redirect('messages');

            } catch (Exception $e) {
                $this->handleError($e, 'Error al crear el mensaje');
            }
        } else {
            // Mostrar formulario de creación
            $data = [
                'token' => $this->generateCsrfToken()
            ];
            $this->view('messages/create', $data);
        }
    }

    private function validateCsrfToken() {
        $token = $_POST['_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419); // CSRF token mismatch
            die('Token CSRF no válido o expirado');
        }
    }

    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function handleError($exception, $defaultMessage = 'Ha ocurrido un error') {
        error_log($exception->getMessage());
        $_SESSION['error_message'] = $defaultMessage;
        if (isset($this->view)) {
            $this->view('errors/500');
        } else {
            die($defaultMessage);
        }
        exit;
    }
}