<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once APPROOT . '/core/Database.php';
require_once APPROOT . '/services/QueueService.php';
require_once APPROOT . '/services/EmailService.php';
require_once APPROOT . '/services/WhatsAppService.php';
require_once APPROOT . '/services/SMSService.php';

// Configurar el tiempo máximo de ejecución (0 = sin límite)
set_time_limit(0);

// Inicializar el servicio de cola
$queueService = new QueueService;

// Obtener mensajes pendientes
$db = new Database;
$db->query('SELECT id FROM messages WHERE status = "pendiente" ORDER BY created_at ASC');
$messages = $db->resultSet();

// Procesar cada mensaje
foreach ($messages as $message) {
    try {
        $queueService->processMessage($message->id);
        echo "Mensaje {$message->id} procesado\n";
    } catch (Exception $e) {
        echo "Error procesando mensaje {$message->id}: {$e->getMessage()}\n";
    }
}

echo "Procesamiento completado\n";