<?php

namespace App\Services;

class ExternalAPIService {
    private $config;
    private $client;

    public function __construct() {
        $this->config = [
            'openai' => [
                'api_key' => $_ENV['OPENAI_API_KEY'],
                'model' => 'gpt-3.5-turbo'
            ],
            'translate' => [
                'api_key' => $_ENV['TRANSLATE_API_KEY'],
                'endpoint' => 'https://translation.googleapis.com/language/translate/v2'
            ],
            'sms' => [
                'api_key' => $_ENV['SMS_API_KEY'],
                'secret' => $_ENV['SMS_API_SECRET']
            ],
            'whatsapp' => [
                'api_key' => $_ENV['WHATSAPP_API_KEY'],
                'secret' => $_ENV['WHATSAPP_API_SECRET']
            ]
        ];

        $this->client = new \GuzzleHttp\Client([
            'timeout' => 30,
            'http_errors' => true
        ]);
    }

    public function analyzeContent($text) {
        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['openai']['api_key'],
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'model' => $this->config['openai']['model'],
                    'messages' => [
                        ['role' => 'system', 'content' => 'Analiza el siguiente texto para marketing:'],
                        ['role' => 'user', 'content' => $text]
                    ]
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $this->logError('openai_analysis', $e->getMessage());
            return null;
        }
    }

    public function translateMessage($text, $targetLang) {
        try {
            $response = $this->client->post($this->config['translate']['endpoint'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['translate']['api_key'],
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'q' => $text,
                    'target' => $targetLang
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $this->logError('translation', $e->getMessage());
            return null;
        }
    }

    public function sendBulkMessages($messages, $channel) {
        $results = [];
        foreach ($messages as $message) {
            try {
                switch ($channel) {
                    case 'sms':
                        $results[] = $this->sendSMS($message);
                        break;
                    case 'whatsapp':
                        $results[] = $this->sendWhatsApp($message);
                        break;
                    default:
                        throw new \Exception('Canal no soportado');
                }
            } catch (\Exception $e) {
                $this->logError($channel . '_send', $e->getMessage());
                $results[] = ['error' => $e->getMessage()];
            }
        }
        return $results;
    }

    private function sendSMS($message) {
        return $this->client->post('https://api.twilio.com/2010-04-01/Accounts/' . $this->config['sms']['api_key'] . '/Messages.json', [
            'auth' => [$this->config['sms']['api_key'], $this->config['sms']['secret']],
            'form_params' => [
                'To' => $message['to'],
                'From' => $message['from'],
                'Body' => $message['content']
            ]
        ]);
    }

    private function sendWhatsApp($message) {
        return $this->client->post('https://graph.facebook.com/v12.0/me/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['whatsapp']['api_key']
            ],
            'json' => [
                'messaging_product' => 'whatsapp',
                'to' => $message['to'],
                'type' => 'text',
                'text' => ['body' => $message['content']]
            ]
        ]);
    }

    private function logError($type, $message) {
        error_log("[ExternalAPI] [{$type}] Error: {$message}");
    }
}