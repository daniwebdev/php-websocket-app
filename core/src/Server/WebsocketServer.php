<?php

namespace IOC\Websocket\Server;

use WebSocket\Server;
use WebSocket\Connection;
use IOC\Websocket\Router\Router;
use IOC\Websocket\Events\EventDispatcher;

class WebsocketServer
{
    private Server $server;
    private $routes;
    private Router $router;
    private EventDispatcher $events;

    /**
     * Initialize WebSocket server with port
     */
    public function __construct(
        private int $port = 8080,
        ?Router $router = null,
        ?EventDispatcher $events = null
    ) {
        $this->server = new Server($this->port);
        $this->router = $router ?? new Router();
        $this->events = $events ?? new EventDispatcher();
    }

    /**
     * Start the WebSocket server
     */
    public function start(): void
    {
        $this->server->onHandshake(function ($server, $connection) {
            $request = $connection->getHandshakeRequest();
            $path = $request->getUri()->getPath();
            
            if ($this->handleConnect($path, $connection)) {
                echo "New connection: {$connection->getRemoteName()} on path: {$path}\n";
            } else {
                $connection->close();
                echo "Connection rejected for path: {$path}\n";
            }
        });

        $this->server->onText(function ($server, $connection, $message) {
            $this->handleMessage($connection, $message);
        });

        $this->server->onClose(function ($server, $connection) {
            echo "Connection closed: {$connection->getRemoteName()}\n";
        });

        $this->server->start();
    }

    private function handleConnect($path, $connection) 
    {
        echo 'ok nice to meet you: ' . $connection->getRemoteName() . "\n";
        
        if (!isset($this->routes['connect'][$path])) {
            return false;
        }

        [$handler, $method] = $this->routes['connect'][$path];
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                return $instance->$method($connection);
            }
        }
        return false;
    }

    private function handleMessage(Connection $connection, $message) 
    {
        $data = json_decode($message, true);
        if (!isset($data['event']) || !isset($data['payload'])) {
            return;
        }

        $request = $connection->getHandshakeRequest();
        $path = $request->getUri()->getPath();
        $event = $data['event'];

        if (!isset($this->routes['events'][$event][$path])) {
            return;
        }

        [$handler, $method] = $this->routes['events'][$event][$path];
        if (class_exists($handler)) {
            $instance = new $handler();
            if (method_exists($instance, $method)) {
                $instance->$method($this->server, $connection, $data['payload']);
            }
        }
    }

    public function routeLoader($routes): void
    {
        $this->routes = $routes->getRoutes();
    }

    /**
     * Load routes configuration
     */
    public function loadRoutes(Router $router): void
    {
        $this->router = $router;
    }
}