<?php

namespace App\Controllers;

use App\Services\ExternalAPIService;

class IntegrationController {
    private $apiService;

    public function __construct() {
        $this->apiService = new ExternalAPIService();
    }

    public function analyzeMessage() {
        $content = $_POST['content'] ?? '';
        if (empty($content)) {
            return $this->jsonResponse(['error' => 'Contenido vacío'], 400);
        }

        $analysis = $this->apiService->analyzeContent($content);
        return $this->jsonResponse(['analysis' => $analysis]);
    }

    public function translateMessage() {
        $content = $_POST['content'] ?? '';
        $targetLang = $_POST['target_lang'] ?? 'en';

        if (empty($content)) {
            return $this->jsonResponse(['error' => 'Contenido vacío'], 400);
        }

        $translation = $this->apiService->translateMessage($content, $targetLang);
        return $this->jsonResponse(['translation' => $translation]);
    }

    public function sendBulkMessages() {
        $messages = $_POST['messages'] ?? [];
        $channel = $_POST['channel'] ?? '';

        if (empty($messages) || empty($channel)) {
            return $this->jsonResponse(['error' => 'Datos incompletos'], 400);
        }

        $results = $this->apiService->sendBulkMessages($messages, $channel);
        return $this->jsonResponse(['results' => $results]);
    }

    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}