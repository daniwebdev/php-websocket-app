<?php

namespace App\Handlers;

use WebSocket\Connection;
use WebSocket\Server;
use WebSocket\Message\Text;
use IOC\Websocket\Contracts\HandlerInterface;

class ChatHandler implements HandlerInterface
{
    /**
     * Handle incoming WebSocket connection
     */
    public function onConnect(Connection $connection): bool
    {
        // Accept all connections to /chat endpoint
        return true;
    }

    /**
     * Handle incoming WebSocket message
     */
    public function onMessage(Server $server, Connection $connection, mixed $payload): void
    {
        // Broadcast the message to all connected clients
        $connections = $server->getConnections();
        $message = json_encode([
            'event' => 'chat.message',
            'payload' => $payload
        ]);
        
        foreach ($connections as $client) {
            if ($client !== $connection) {
                $client->send(new Text($message));
            }
        }
    }
}