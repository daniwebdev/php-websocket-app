<?php

namespace IOC\Websocket\Contracts;

use WebSocket\Connection;
use WebSocket\Server;

interface HandlerInterface
{
    /**
     * Handle incoming WebSocket connection
     */
    public function onConnect(Connection $connection): bool;

    /**
     * Handle incoming WebSocket message
     */
    public function onMessage(Server $server, Connection $connection, mixed $payload): void;
}