<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class ActivityServer implements MessageComponentInterface {
    protected $clients;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        foreach ($this->clients as $client) {
            if ($client !== $from) {
                $client->send(json_encode($data));
            }
        }
    }
    
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
}