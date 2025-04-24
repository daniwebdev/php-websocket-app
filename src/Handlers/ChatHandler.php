<?php

namespace App\Handlers;

use App\Events\ChatEvents;
use WebSocket\Connection;

class ChatHandler
{
    protected $connections = [];

    public function onConnect($connection) {
        // Return true to accept the connection, false to reject
        echo 'chat connection accepted: ' . $connection->getRemoteName() . "\n";
        return true;
    }

    public function onMessage($server, $connection, $payload) {
        // Handle chat message
        $response = json_encode([
            'event' => 'chat.message',
            'payload' => $payload
        ]);
        
        // Broadcast message to all connected clients
        $connection->getServer()->broadcast($response);
    }

    public function broadcast(string $message)
    {
        foreach ($this->connections as $connection) {
            $connection->send($message);
        }
    }

    public function addConnection(Connection $connection)
    {
        $this->connections[] = $connection;
    }

    public function removeConnection(Connection $connection)
    {
        $this->connections = array_filter($this->connections, function ($conn) use ($connection) {
            return $conn !== $connection;
        });
    }
}