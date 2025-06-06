<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\ActivityServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new ActivityServer()
        )
    ),
    8080
);

$server->run();