<?php
class WhatsAppService {
    private $client;
    private $fromNumber;

    public function __construct() {
        $this->client = new \Twilio\Rest\Client(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);
        $this->fromNumber = WHATSAPP_FROM_NUMBER;
    }

    public function sendMessage($to, $message) {
        try {
            $result = $this->client->messages->create(
                "whatsapp:" . $to,
                [
                    "from" => "whatsapp:" . $this->fromNumber,
                    "body" => $message
                ]
            );
            return $result->sid;
        } catch (\Exception $e) {
            error_log('Error al enviar WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    public function getMessageStatus($messageSid) {
        try {
            $message = $this->client->messages($messageSid)->fetch();
            return $message->status;
        } catch (\Exception $e) {
            error_log('Error al obtener estado de WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}